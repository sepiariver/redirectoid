<?php
/**
 * Redirectoid
 * @author YJ Tso
 * @copyright Copyright 2013, YJ Tso
 *
 * No warranties, GPL license: see included license.txt file
 * 
 * DESCRIPTION
 *
 * Redirects to the Resource with the ID set in the &id property,
 * or optionally to a random child of defined parent(s). 
 * Supports 301, 302, 303, and 307 Response Codes as per HTTP/1.1 Protocol.
 *
 * PARAMETERS:
 *
 * &default			ID of default target Resource. Default: site_start system setting.
 * &id              ID of target Resource. Can be a string: 'random' or 'firstChild'. Default: $default
 * &context         Context of target Resource. Default: 'web'
 * &urlParamString  URL parameter string to send with the redirected request
 * &scheme          Scheme for $modx->makeUrl to use. Default: link_tag_scheme system setting.
 * &parents         Comma-separated list of parent IDs for random child mode. Defaults to current Resource
 * 					Note: if more than 1 parent ID is provided, 'firstChild' mode can be ambiguous.
 * &showHidden      Set to 1 to include Resources hidden from menus, in random child mode. Defaults to 0
 * &showDeleted     Set to 1 to include deleted Resources, in random child mode. Defaults to 0
 * &showUnpublished Set to 1 to include unpublished Resources, in random child mode. Defaults to 0
 * &responseCode    '302', '303' or '307'. Set this to modify the response code sent to the client
 *                  Default: '' which sends '301'
 *
 * USAGE EXAMPLES:
 *
 * This redirects to the Resouce ID specified in the site_start system setting, with a '301 Moved Permanently' response.
 * [[Redirectoid]]
 * 
 * This redirects to Resource ID '12' with a '307 Temporary Redirect' response.
 * [[Redirectoid? &id=`12` &responseCode=`307`]]
 * 
 * This redirects to Resource ID '55' in the 'custom' context, with a url parameter 'service=logout'.
 * [[Redirectoid? &id=`55` &context=`custom` &urlParamString=`service=logout`]]
 * 
 * This redirects to a random child Resource of the current Resource with a '307 Temporary Redirect' response.
 * [[!Redirectoid? &id=`random` &responseCode=`307`]]
 * 
 * This redirects to a random child Resource of the specified parent, even if hidden from menus.
 * [[!Redirectoid? &id=`random` &parents=`3,56,821` &showHidden=`1`]]
 *
 */
// Set options
$default = (int) $modx->getOption('default', $scriptProperties, $modx->getOption('site_start'), true);
if ($default < 1) {
	$default = $modx->getOption('site_start');
}
$id = $modx->getOption('id', $scriptProperties, $default, true); // mixed types: string, int
$context = $modx->getOption('context', $scriptProperties, $modx->context->get('key'), true);
$params = trim($modx->getOption('urlParamString', $scriptProperties, ''), '?');
$scheme = $modx->getOption('scheme', $scriptProperties, $modx->getOption('link_tag_scheme'), true);
$parents = array_filter(array_map('trim', explode(',', $modx->getOption('parents', $scriptProperties, $modx->resource->get('id'), true))));
$showHidden = (int) $modx->getOption('showHidden', $scriptProperties, 0);
$showDeleted = (int) $modx->getOption('showDeleted', $scriptProperties, 0);
$showUnpublished = (int) $modx->getOption('showUnpublished', $scriptProperties, 0);

//Set redirect status in accordance with HTTP/1.1 protocol defined here: http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
$defaultCode = ($id === 'random') ? '307' : '301';
$responseCode = $modx->getOption('responseCode', $scriptProperties, $defaultCode, true);
switch ($responseCode) {
    case '302': $redirectStatus = 'HTTP/1.1 302 Found'; break;
    case '303': $redirectStatus = 'HTTP/1.1 303 See Other'; break;
    case '307': $redirectStatus = 'HTTP/1.1 307 Temporary Redirect'; break;
    default: $redirectStatus = 'HTTP/1.1 301 Moved Permanently';
}

// Handle ID
if (strtolower($id) === 'random' || strtolower($id) === 'rand') {
	$id = 'random';
} elseif (strtolower($id) === 'firstchild' || strtolower($id) === 'first') {
	$id = 'firstChild';
} else {
	// Early result if ID is not 'random' or 'firstChild'
	$id = (empty($id)) ? abs($default) : (int) $id;
	// Make the URL and send
	$url = $modx->makeUrl($id, $context, $params, $scheme);
	$modx->sendRedirect($url, ['responseCode' => $redirectStatus]);
}

$c = $modx->newQuery('modResource');
$where = array(
	'parent:IN' => $parents,
	'deleted' => 0,
);
if (!$showHidden) $where['hidemenu'] = 0;
if (!$showUnpublished) $where['published'] = 1;
if (!$showDeleted) $where['deleted'] = 0;

$c->where($where);

if ($id === 'random') $c->sortby('RAND()');
if ($id === 'firstChild') $c->sortby('menuindex', 'ASC');
$c->limit(1);
$c->select('id');
$id = $modx->getValue($c->prepare());

// Last chance
$id = (empty($id)) ? abs($default) : (int) $id;
// Make the URL and send
$url = $modx->makeUrl($id, $context, $params, $scheme);
$modx->sendRedirect($url, ['responseCode' => $redirectStatus]);