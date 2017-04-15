# twigstrap

using Twig templates for [Bootstrap 4](https://v4-alpha.getbootstrap.com/) in [CakePHP 3](https://cakephp.org)

Pulls in  [WyriHaximus/TwigView](https://github.com/WyriHaximus/TwigView) for parsing twig files.  

Ideas and most code for Helpers taken from [Friendsofcake/BootstrapUI](https://github.com/FriendsOfCake/bootstrap-ui)

## This is work in Progress. Don't use it for now!

### adding Plugin

add 

    "repositories": [
             {
                "type": "vcs",
                "url": "https://github.com/cewi/twigstrap"
            }
        ] 
        
 to your composer.json. Then in console:

```
composer require cewi/twigstrap:dev-master
```

Load the Plugin in bootstrap.php:

```
Plugin::load('cewi/twigstrap');
```

you must load TwigView Plugin, too:

```
Plugin::load('WyriHaximus/TwigView', [
    'bootstrap' => true,
]);
```

### use view class

Instead of extending from the View let AppView extend TwigstrapView (which in turn extends WyriHaximus/TwigView):

```
namespace App\View;

use Twigstrap\View\TwigstrapView;

class AppView extends TwigstrapView
{
}
```

### baking views:
```
 ./bin/cake bake twig_template {Model} -t Twigstrap
```



