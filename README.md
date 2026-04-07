# Rubricate UserAgent

[![Maintainer](http://img.shields.io/badge/maintainer-@estefanionsantos-blue.svg?style=flat-square)](https://estefanionsantos.github.io/)
[![Source Code](http://img.shields.io/badge/source-rubricate/agent-blue.svg?style=flat-square)](https://github.com/rubricate/agent)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/rubricate/agent.svg?style=flat-square)](https://packagist.org/packages/rubricate/agent)
[![Latest Version](https://img.shields.io/github/release/rubricate/agent.svg?style=flat-square)](https://github.com/rubricate/agent/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/rubricate/agent.svg?style=flat-square)](https://packagist.org/packages/rubricate/agent)

#### Last Version
```
$ composer require rubricate/agent
```

Documentation is at https://rubricate.github.io/components/agent


#### Preparing the Configuration Data
```php
<?php

$config = [
    'platforms' => [
        'windows nt 10.0' => 'Windows 10',
        'android'         => 'Android',
        'iphone'          => 'iPhone',
    ],
    'browsers' => [
        'Chrome'  => 'Chrome',
        'Firefox' => 'Firefox',
        'MSIE'    => 'Internet Explorer',
    ],
    'mobiles' => [
        'iphone'   => 'Apple iPhone',
        'android'  => 'Android Device',
    ],
    'robots' => [
        'googlebot' => 'Googlebot',
    ]
];

```

#### Preparing the Configuration Data
```php

<?php

use Rubricate\Agent\UserAgent;

// 1. Instantiate the class passing the configurations
$ua = new UserAgent($config);

// 2. Checking the device type
if ($ua->isMobile()) {
    echo "You are using a mobile device: " . $ua->getMobile();

} elseif ($ua->isRobot()) {
    echo "Hello, robot: " . $ua->getRobot();

} else {
    echo "You are on a Desktop.";
}

echo "<br>";

// 3. Obtaining specific browser information
if ($ua->isBrowser()) {
    echo "Browser: " . $ua->getBrowser() . " (Version: " . $ua->getVersion() . ")";
}

echo "<br>"; // 4. Checking the Platform (OS)
echo "Operating System: " . $ua->getPlatform();

echo "<br>";

/ 5. Accepted Languages
echo "Preferred Languages: " . implode(', ', $ua->getLanguages());

/ 6. Specific Boolean Check
if ($ua->acceptLang('pt-br')) {
    echo "The user accepts Brazilian Portuguese.";
}

```



## Credits

- [Estefanio N Santos](https://github.com/estefanionsantos) (Developer)
- [All Contributors](https://github.com/rubricate/agent/contributors) (Let's program)

## License

The MIT License (MIT). Please see [License File](https://github.com/rubricate/agent/master/LICENSE) for more
information.


