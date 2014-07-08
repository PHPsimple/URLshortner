<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    class URLshortner
    {
        /*
        *    Database credentials
        */
        protected $db_conn;
        protected $db_host     = '127.0.0.1';
        protected $db_user     = 'root';
        protected $db_password = '';
        protected $db_database = 'shortner';

        /*
        *    URL
        */
        protected $url;
        protected $url_length = 6;

        public function __construct()
        {
            /*
            *    Checking if URL is set
            */
            (!empty($_GET['url']) ? $this->url = $_GET['url'] : $this->url = '');

            /*
            *    Checking if database can be accessed
            */
            if($this->db_connect())
            {
                echo "Database connected <br />";
                if(isset($_POST['new_url'])) {
                    $this->newURL();
                    echo "Inserting <br />";
                }

                if($this->URLcheck()) { $this->URLredirect(); }
            }

            echo $this->URLcheck();
        }

        /*
        *    If URL is set, this method will check if the url is a shorted one
        */
        protected function URLcheck()
        {
            if(!empty($this->url))
            {
                if(strpos($this->url, 'L') === 0)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }

        /*
        *     With this method cthis class will be connecting to a database
        */
        protected function db_connect()
        {
            $conn = new mysqli($this->db_host, $this->db_user, $this->db_password, $this->db_database);
            $this->db_conn = $conn;

            return true;
        }

        /*
        *    With this method user can populate database with URLs
        */
        protected function newURL()
        {
            $code = $this->codeURL();
            $url = $_POST['new_url'];

            $query = "INSERT INTO url(code, url) VALUES('".$code."', '".$url."')";
            $this->db_conn->query($query);

            return true;
        }

        /*
        *    This method will generate URL code
        */
        protected function codeURL()
        {
            $code = 'L';

            for($i = 0; $i <= $this->url_length; $i++)
            {
                $random = rand(0, 20);
                if($random <= 10)
                {
                    $code .= chr(rand(65, 90));
                }
                else
                {
                    $code .= chr(rand(97, 122));
                }
            }

            return $code;
        }

        /*
        *    This method will redirect the user based on the code he was given
        */
        protected function URLredirect()
        {
            $query = "SELECT * FROM url  WHERE code='".$this->url."'";
            $result = $this->db_conn->query($query);

            if($result->num_rows == 1)
            {
                while($row = $result->fetch_assoc())
                {
                    header("Location: " . $row['url']);
                    exit();
                }
            }
        }
    }

    $url = new URLshortner;
