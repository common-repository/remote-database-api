<?php

/**
 * Class AirtableApiRequest
 * -----------------------
 */

class AirtableApiRequest extends AirtableApiCore {

    private static $base = '';
    private static $table = '';
    private static $view = '';

    public function __construct($base = null, $table = null, $view = "") {
        if($base != null)
            self::$base = $base;
        else
            self::$base = self::get_option('airtable_database');

        if($table != null)
            self::$table = $table;

        if($view != null)
            self::$view = $view;
        else
            self::$view = 'Main View';
    }

    /*

                CRUD FUNCTIONS

    */

    public function create($data)
    {
        return $this->call(
            array(
                'type' => 'create',
                'parameters' => array(
                    'data' => $data
                )
            )
        );
    }

    public function get($args, $cache = true)
    {
        if($args == null)
        {
            return $this->call(
                array(
                    'type' => 'all'
                ),
                $cache
            );
        }
        else
        {
            return $this->call($args, $cache);
        }
    }

    public function retrieve($args)
    {
        return $this->call(
            array(
                'type' => 'id',
                'parameters' => $args
                )
        );
    }

    public function update($record_id, $data)
    {
        return $this->call(
            array(
                'type' => 'update',
                'parameters' => array(
                    'id' => $record_id,
                    'data' => $data
                )
            )
        );
    }

    public function delete()
    {

    }

    /*

                CURL FUNCTIONS

    */

    private function call($args = array(), $cache = true)
    {

        /*
         * $args
         * > type => 'all', 'select', 'id', 'update', 'create'
         * > parameters => array / id
         */

        $request_url = "";
        $parameters = null;

        if(is_array($args) && !isset($args['parameters']))
            $parameters = array();
        else
            $parameters = $args['parameters'];

        if(is_array($args) && (!isset($args['type']) || $args['type'] == ""))
            $args['type'] = 'all';

        if($args['type'] == 'all' || $args['type'] == 'select')
        {
            // Get all entries in View

            $request_url = AIRTABLEAPI_AIRTABLE_API . self::$base . '/' . urlencode ( self::$table );

            if(!isset($parameters['view']) || $parameters['view'] == '')
                $parameters['view'] = self::$view;

            if(!isset($parameters['limit']) || $parameters['limit'] == '')
                $parameters['limit'] = '100';

        }
        elseif ($args['type'] == 'id' && !empty($parameters['id']))
        {
            // Get specific entry
            $request_url = AIRTABLEAPI_AIRTABLE_API . self::$base . '/' . urlencode ( self::$table ) . '/' . $parameters['id'];
        }
        elseif ($args['type'] == 'update' && !empty($parameters['id']) && !empty($parameters['data']))
        {
            // Update record
            $request_url = AIRTABLEAPI_AIRTABLE_API . self::$base . '/' . urlencode ( self::$table ) . '/' . $parameters['id'];
        }
        elseif ($args['type'] == 'create' && !empty($parameters['data']))
        {
            // Create a record
            $request_url = AIRTABLEAPI_AIRTABLE_API . self::$base . '/' . urlencode ( self::$table );
        }
        else
        {
            return false;
        }

        // CURL PART

        $curl = curl_init();

        $authorization = "Authorization: Bearer " . AirtableApiAdmin::get_option('airtable_apikey');

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json' , $authorization )
        ));

        // GET > ALL, SELECT, ID

        if(     $args['type'] == 'all'
            ||  $args['type'] == 'select'
            ||  $args['type'] == 'id')
        {
            $cached_value = $this->get_cache_data($this->get_cache_slug($request_url, $parameters));

            if(($cached_value != false && $cache) && AirtableApiAdmin::get_options('airtable_cache_lifetime') > 0)
            {
                return $cached_value;
            }
            else
            {
                $request_offset = '';
                $records = array();

                while ( ! is_null ( $request_offset ) ) {

                    if($args['type'] != 'id' && $request_offset != '') $parameters['offset'] = $request_offset;
                    $request_curl_url = $request_url . $this->curl_parameters($parameters, array('id'));

                    curl_setopt( $curl, CURLOPT_URL, $request_curl_url );

                    // CURL

                    $curl_response = curl_exec( $curl );

                    if ( curl_errno( $curl ) != 0 )
                        return array('error' => 'curl', 'data' => curl_errno( $curl ));

                    // AIRTABLE

                    $airtable_response = json_decode( $curl_response, TRUE );

                    if ( array_key_exists ( 'error', $airtable_response ) )
                        return array('error' => 'airtable', 'data' => $airtable_response['error']);

                    if($args['type'] != 'id')
                    {
                        foreach ( $airtable_response['records'] as $record ) {
                            $records[ $record['id'] ] = $record;
                        }

                        $request_offset = $airtable_response['offset'];
                    }
                    elseif($args['type'] == 'id')
                    {
                        if($cache) {
                            $this->cache_data(
                                $this->get_cache_slug($request_url, $parameters),
                                $airtable_response,
                                AirtableApiAdmin::get_options('airtable_cache_lifetime')
                            );
                        }

                        curl_close($curl);
                        return $airtable_response;
                    }

                    if(!empty($parameters['maxRecords']) && $parameters['maxRecords'] < 100)
                        $request_offset = null;

                }

                curl_close($curl);
                
                if($cache)
                {
                    $this->cache_data(
                        $this->get_cache_slug($request_url, $parameters),
                        $records,
                        AirtableApiAdmin::get_options('airtable_cache_lifetime')
                    );
                }

                return $records;
            }
        }
        elseif (
                $args['type'] == 'create'
            ||  $args['type'] == 'update'
        )
        {
            if($args['type'] == 'create')
                curl_setopt($curl, CURLOPT_POST, count($parameters['data']));
            else
                curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'PATCH');

            curl_setopt( $curl, CURLOPT_URL, $request_url );
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('fields' => $parameters['data'])));

            $result = curl_exec($curl);

            if(!CURL_ERROR($curl))
            {
                curl_close($curl);
                return json_decode($result);
            }
            else
            {
                return array('error' => 'curl', 'data' => curl_errno( $curl ));
            }

        }

        curl_close($curl);
    }

    private function curl_parameters($parameters = array(), $exclude = array())
    {
        $parameters_request = "";

        if(is_array($parameters) && count($parameters) > 0)
        {
            foreach ($parameters as $key => $value)
            {
                // Exclude record id key from url parameters
                if($key == in_array($key, $exclude)) continue;

                if($parameters_request == "")
                    $parameters_request .= "?";
                else
                    $parameters_request .= "&";

                $parameters_request .= $key . '=' . urlencode($value);
            }
        }

        return $parameters_request;
    }

    /*

                CACHE FUNCTIONS

    */

    private function get_cache_slug($request_url, $parameters)
    {
        unset($parameters['limit']);
        unset($parameters['offset']);
        unset($parameters['id']);

        return $this->get_plugin_slug() . '_' . sha1($request_url . $this->curl_parameters($parameters));
    }

    private function cache_data($slug, $data, $expiration = HOUR_IN_SECONDS)
    {
        if(!$expiration || empty($expiration) || $expiration = 0)
            return false;

        return set_transient( $slug, $data, $expiration);
    }

    private function get_cache_data($transient_slug)
    {
        $transient = get_transient( $transient_slug );

        if ( false === (  $transient ) ) {
            return false;
        }

        return $transient;

    }
}