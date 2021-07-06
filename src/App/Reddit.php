<?php

// Some of the code are referred to jstolpe (https://github.com/jstolpe/blog_code/tree/master/reddit_api_php)

namespace Osky\App;

use Carbon\Carbon;

class Reddit {
    private $_client_id;
    private $_secret;
    private $_username;
    private $_password;
    private $_redirect;

    private $_access_token;
    private $_token_type;

    private $_subreddit;
    private $_term;
    
    private $title_limit = 30;
    private $selftext_limit = 20;
    
    public $_data;
    public $_count;

    private $url =
    [
        'access-token' => 'https://www.reddit.com/api/v1/access_token',
        'search-subreddit' => 'https://oauth.reddit.com/r/{value1}/new/.json'
    ];
    
    public function __construct($subreddit, $term)
    {
        $this->_client_id = env('REDDIT_CLIENT_ID');
        $this->_secret = env('REDDIT_SECRET');
        $this->_username = env('REDDIT_USERNAME');
        $this->_password = env('REDDIT_PASSWORD');
        $this->_redirect = env('REDDIT_REDIRECT');

        $this->_subreddit = $subreddit;
        $this->_term = $term;

        return $this;
    }

    public function generateToken()
    {
        // curl settings and call to reddit
        $ch = curl_init($this->url['access-token']);
        curl_setopt( $ch, CURLOPT_USERPWD, $this->_client_id . ':' . $this->_secret );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, [
            'grant_type' => 'password',
            'username' => $this->_username,
            'password' => $this->_password
        ]);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

        // curl response from reddit
        $response_raw = curl_exec( $ch );
        $response = json_decode( $response_raw );
        curl_close($ch);

        // display response from reddit
        $this->_access_token = $response->access_token;
        $this->_token_type = $response->token_type;
        
        return $this;
    }

    public function search()
    {
        // curl settings and call to post to the subreddit
        $ch = curl_init(str_replace('{value1}',$this->_subreddit,$this->url['search-subreddit']));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_USERAGENT, $this->_username . ' by /u/' . $this->_username . ' (Phapper 1.0)' );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( "Authorization: " . $this->_token_type . " " . $this->_access_token ) );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, [
            'limit' => 100,
        ] );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

        // curl response from our post call
        $response_raw = curl_exec( $ch );
        $response = json_decode( $response_raw );
        curl_close( $ch );
        
        $this->_data = [];
        if($response != null)
        {
            $temp_children = $response->data->children;
            $this->_count = 0;
            foreach($temp_children as $child)
            {
                $temp_data = $child->data;
                if(strpos(strtolower($temp_data->title), $this->_term) !== false && (strpos(strtolower($temp_data->selftext), $this->_term) !== false && $temp_data->selftext != "")) {
                    array_push($this->_data, [
                        "title" => $temp_data->title,
                        "selftext" => $temp_data->selftext,
                        "created" => Carbon::parse($temp_data->created)->format("Y-m-d H:i:s"),
                        "url" => $temp_data->url
                    ]);
                    $this->_count++;
                }
            }
        }

        return $this;
    }

    public function sort()
    {
        sort($this->_data);

        return $this;
    }

    public function trim()
    {
        for($i = 0; $i < $this->_count; $i++) {
            if(strlen($this->_data[$i]['title']) > 30) {
                $this->_data[$i]['title'] = substr($this->_data[$i]['title'], 0, $this->title_limit).'...';
            }

            $term_length = strlen($this->_term);
            $selftext = $this->_data[$i]['selftext'];
            $selftext_length = strlen($selftext);
            $term_position = strpos($selftext, $this->_term);
            if($term_position < $this->selftext_limit)
            {
                $this->_data[$i]['selftext'] = substr($selftext, 0, $term_position+$this->selftext_limit)."...";
            }
            else if($selftext_length - ($term_position + $term_length) < $this->selftext_limit)
            {
                $this->_data[$i]['selftext'] = '...'.substr($selftext, $term_position-$this->selftext_limit, $selftext_length);
            }
            else
            {
                $this->_data[$i]['selftext'] = '...'.substr($selftext, $term_position-$this->selftext_limit, $term_length+($this->selftext_limit*2)).'...'; 
            }
            $this->_data[$i]['selftext'] = str_replace($this->_term, '<fg=#ff6666;options=underscore>'.$this->_term.'</>', $this->_data[$i]['selftext']);
        }

        return $this;
    }

    public function get($columns = [])
    {
        if(count($columns) == 0)
            return $this;
            
        $temp_data = [];
        foreach($this->_data as $data) {
            $temp_row = [];
            foreach($columns as $key) {
                array_push($temp_row, $data[$key]);
            }
            array_push($temp_data, $temp_row);
        }
        $this->_data = $temp_data;

        return $this;
    }
}