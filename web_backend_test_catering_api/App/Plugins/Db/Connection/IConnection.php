<?php


namespace App\Plugins\Db\Connection;


interface IConnection {
    /**
     * Function to get the DSN for the connection
     * @return string
     */
    function getDsn(): string;

    /**
     * Function to get the host
     * @return string
     */
    function getHost(): string;

    /**
     * Function to get the db name
     * @return string
     */
    function getDbName(): string;
    
    /**
     * Function to get the username
     * @return string
     */
    function getUsername(): string;
    
    /**
     * Function to get the password
     * @return string
     */
    function getPassword(): string;
}
