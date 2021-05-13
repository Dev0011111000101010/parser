# text
A simple text object.

```php
namespace Jstewmc\Text;

$text = new Text('foo');

$text->getText();  // returns "foo"
(string) $text;    // returns "foo"
```

This library is a simple text object that can serve as the base class for more complex text objects.

## License

[MIT](https://github.com/jstewmc/text/blob/master/LICENSE)

## Author

[Jack Clayton](mailto:clayjs0@gmail.com)

## License

This library follows [Semantic Versioning](http://semver.org).

### 0.1.1, September 22, 2016

* Update `text` property from `private` to `protected` (whoops!).

### 0.1.0, September 22, 2016

* Initial release
