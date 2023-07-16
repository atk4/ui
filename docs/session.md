:::{php:namespace} Atk4\Ui
:::

# Session Trait

:::{php:trait} SessionTrait
:::

## Introduction

SessionTrait is a simple way to let object store relevant data in the session. Specifically used in ATK UI
some objects want to memorize data. (see https://github.com/atk4/ui/blob/develop/src/Wizard.php#L12)

You would need 3 things. First make use of session trait:

```
use \Atk4\Ui\SessionTrait;
```

next you may memorize any value, which will be stored independently from any other object (even of a same class):

```
$this->memorize('dsn', $dsn);
```

Later when you need the value, you can simply recall it:

```
$dsn = $this->recall('dsn');
```

## Properties

:::{php:attr} rootNamespace
Internal property to make sure that all session data will be stored in one
"container" (array key).
:::

## Methods

:::{php:method} startSession()
Create new session.
:::

:::{php:method} closeSession()
Close existing session.
:::

:::{php:method} memorize($key, $value)
Remember data in object-relevant session data.
:::

:::{php:method} learn($key, $default = null)
Similar to memorize, but if value for key exist, will return it.
:::

:::{php:method} recall($key, $default = null)
Returns session data for this object. If not previously set, then $default
is returned.
:::

:::{php:method} forget($key = null)
Forget session data for arg $key. If $key is omitted will forget all
associated session data.
:::
