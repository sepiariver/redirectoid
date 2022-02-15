# Redirectoid

A simple Snippet that redirects to any Resource specified by ID, or a random child (or first child) of a specified parent(s).

Redirectoid supports various response codes described here: [http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html).

It also exposes the arguments for the `$modx->makeUrl()` method, for further customization.

## USE CASES

Why wouldn't you use a WebLink?

You may have Resources that have content, but are only displayed as sections or blocks in the parent Resource, so the child Resources aren't meant to be viewed on their own. Redirectoid can be called in the child Resources' Template like so:

```
[[Redirectoid? &id=`[[*parent]]`]]
```

As of version 2, you can redirect to a random child of a given parent(s) Resource, so the use cases expand dramatically: split testing, randomization of content or redirects, etc.

### PARAMETERS

- &default          ID of default target Resource. Default: site_start system setting.
- &id               ID of target Resource. Can be a string: 'random' or 'firstChild'. Default:$default
- &context          Context of target Resource. Default: 'web'
- &urlParamString   URL parameter string to send with the redirected request
- &scheme           Scheme for $modx->makeUrl to use. Default: link_tag_scheme system setting.
- &parents          Comma-separated list of parent IDs for random child mode. Defaults tocurrent Resource
				    Note: if more than 1 parent ID is provided, 'firstChild' mode can beambiguous.
- &showHidden       Set to 1 to include Resources hidden from menus, in random child mode.Defaults to 0
- &showDeleted      Set to 1 to include deleted Resources, in random child mode. Defaults to 0
- &showUnpublished  Set to 1 to include unpublished Resources, in random child mode. Defaultsto 0
- &responseCode     '302', '303' or '307'. Set this to modify the response code sent to theclient
                    Default: '' which sends '301'

### USAGE EXAMPLES

This redirects to the Resouce ID specified in the site_start system setting, with a '301 Moved Permanently' response.

```
[[Redirectoid]]
```

This redirects to Resource ID '12' with a '307 Temporary Redirect' response.

```
[[Redirectoid? &id=`12` &responseCode=`307`]]
```

This redirects to Resource ID '55' in the 'custom' context, with a url parameter 'service=logout'.

```
[[Redirectoid? &id=`55` &context=`custom` &urlParamString=`service=logout`]]
```

This redirects to a random child Resource of the current Resource with a '307 Temporary Redirect' response.

```
[[!Redirectoid? &id=`random` &responseCode=`307`]]
```

This redirects to a random child Resource of the specified parent, even if hidden from menus.

```
[[!Redirectoid? &id=`random` &parents=`3,56,821` &showHidden=`1`]]
```
