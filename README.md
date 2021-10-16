# Redirectoid

A simple Snippet that redirects to any Resource specified by ID, or a random child (or first child) of a specified parent(s).

Redirectoid supports various response codes described here: [http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

It also exposes the arguments for the `$modx->makeUrl()` method, for further customization.

### USE CASES

Why wouldn't you use a WebLink? 

You may have Resources that have content, but are only displayed as sections or blocks in the parent Resource, so the child Resources aren't meant to be viewed on their own. Redirectoid can be called in the child Resources' Template like so: 

```
[[Redirectoid? &id=`[[*parent]]`]]
```

As of version 2, you can redirect to a random child of a given parent(s) Resource, so the use cases expand dramatically: split testing, randomization of content or redirects, etc.

### PARAMETERS

- &id               ID of target Resource. Can be a string: 'random' or 'firstChild'. Default: site_start system setting
- &context          Context of target Resource. Default: ''
- &urlParamString   URL parameter string to send with the redirected request. Default ''
- &scheme           Scheme for `$modx->makeUrl()` to use. Default: -1
- &parents          Comma-separated list of parent IDs for random child mode. Defaults to current Resource
- &showHidden       Set to 1 to include Resources hidden from menus, in random child mode. Defaults to 0
- &showDeleted      Set to 1 to include deleted Resources, in random child mode. Defaults to 0
- &showUnpublished  Set to 1 to include unpublished Resources, in random child mode. Defaults to 0
- &responseCode     '302', '303' or '307'. Set this to modify the response code sent to the client. Default: '' which sends '301' or '307' when `&id` is 'random'
- &useCtxMap        Set to 1 to use the MODX Context Resource Map, in random child mode. Faster but doesn't allow queries like showing deleted Resources. Defaults to 0
- &depth            Depth to pass to `$modx->getChildIds()` in random child mode when `&useCtxMap` is truth-y. Defaults to 1

### USAGE EXAMPLES

This redirects to the Resource ID specified in the `site_start` system setting, with a '301 Moved Permanently' response:

```
[[Redirectoid]]
```

This redirects to Resource ID '12' with a '307 Temporary Redirect' response:

```
[[Redirectoid?id=`12` &responseCode=`307`]]
```

This redirects to Resource ID '55' in the 'custom' context, with a url parameter 'service=logout':

```
[[Redirectoid?id=`55` &context=`custom` &urlParamString=`service=logout`]]
```

This redirects to a random child Resource of the current Resource with a '307 Temporary Redirect' response:

```
[[!Redirectoid?id=`random` &responseCode=`307`]]
```

This redirects to a random child Resource of the specified parent, even if hidden from menus:

```
[[!Redirectoid?id=`random` &parents=`3,56,821` &showHidden=`1`]]
```