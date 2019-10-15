<?php

require __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * for dev purposes
 */

/*
$_ENV = [
    'VHOST_HANS_PROXY_URL' => 'http://dude:80',
    //'VHOST_HANS_SERVERNAME' => 'hans.${DOMAIN}',
    'VHOST_HANS_REGEXP' => '*.fred.vcap.me',
    'VHOST_HANS_CACERT' => true,

    'TLS_KEY' => '/tls.key',
    'TLS_CERT' => '/tls.crt',

    //'CA_CERT_PREFIXES' => 'mongoui,jenkins'

];
*/


$vhostPrefix = 'VHOST_';
$vhosts = [];
foreach ($_ENV as $envName => $envValue) {
    if (substr($envName, 0, strlen($vhostPrefix)) == $vhostPrefix) {
        $nameParts = explode('_', $envName);
        if (count($nameParts) < 3) {
            echo 'ERROR ON ENV "'.$envName.'" => not matching naming convention, skipping...'.PHP_EOL;
            continue;
        }

        $vhosts[$nameParts[1]][implode('_', array_slice($nameParts, 2))] = $envValue;
        unset($_ENV[$envName]);
    }
}

$_ENV['vhosts'] = $vhosts;

//var_dump($_ENV);


$environment = array_merge($_ENV, []);

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, []);

echo $twig->render('traefik-conf.twig', $environment);
