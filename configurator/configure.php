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
    'VHOST_HANS_SERVERNAME' => 'hans.${DOMAIN}',
    //'VHOST_HANS_REGEXP' => '*.fred.vcap.me',
    'VHOST_HANS_CACERT' => true,
    'VHOST_HANS_BASICAUTH' => 'BASIC',

    'ACME' => 'true',

    'TLS_KEY' => '/tls.key',
    'TLS_CERT' => '/tls.crt',

    'BASICAUTH_BASIC' => 'hhh:hhh'

];
*/


$vhostPrefix = 'VHOST_';
$vhosts = [];
$basicAuthPrefix = 'BASICAUTH_';
$basicAuths = [];
foreach ($_ENV as $envName => $envValue) {
    // vhosts
    if (substr($envName, 0, strlen($vhostPrefix)) == $vhostPrefix) {
        $nameParts = explode('_', $envName);
        if (count($nameParts) < 3) {
            echo 'ERROR ON ENV "'.$envName.'" => not matching naming convention, skipping...'.PHP_EOL;
            continue;
        }

        $vhosts[$nameParts[1]][implode('_', array_slice($nameParts, 2))] = $envValue;
        unset($_ENV[$envName]);
    }

    // basicauths
    if (substr($envName, 0, strlen($basicAuthPrefix)) == $basicAuthPrefix) {
        $nameParts = explode('_', $envName);
        if (count($nameParts) < 2) {
            echo 'ERROR ON ENV "'.$envName.'" => not matching naming convention, skipping...'.PHP_EOL;
            continue;
        }

        $basicAuths[$nameParts[1]] = $envValue;
        unset($_ENV[$envName]);
    }
}

$_ENV['vhosts'] = $vhosts;
$_ENV['basicAuths'] = $basicAuths;

$environment = array_merge($_ENV, []);

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, []);

echo $twig->render('traefik-conf.twig', $environment);
