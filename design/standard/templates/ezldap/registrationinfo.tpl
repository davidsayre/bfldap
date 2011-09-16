{set-block scope=root variable=content_type}text/html{/set-block}
<html><body>

{def $site_name = ezini('SiteSettings', 'SiteName')
     $site_url = ezini('SiteSettings','SiteURL')}
{set-block scope=root variable=subject}{'%1 registration info'|i18n('design/standard/user/register',,array($site_name))}{/set-block}
{def $userlink = concat("http://", $hostname, concat('content/view/full/',$object.main_node_id)|ezurl(no))}
{def $confirmlink = concat("http://", $hostname, concat('user/activate/', $hash, '/', $object.main_node_id)|ezurl(no))}

{'Thank you for registering at %sitename.'|i18n('design/standard/user/register',,hash('%sitename',$site_name))}<br/><br/>

{'Your account information'|i18n('design/standard/user/register')}<br/><br/>


{'Username'|i18n('design/standard/user/register')}: {$user.login}<br/>
{'Email'|i18n('design/standard/user/register')}: {$user.email}<br/>

{section show=$password}
{'Password'|i18n('design/standard/user/register')}: {$password}<br/>
{/section}
<br/>

{section show=and( is_set( $hash ), $hash )}
{'Click the following URL to confirm your account'|i18n('design/standard/user/register')}<br/>

<a href="{$confirmlink}">{$confirmlink}</a><br/><br/>

{/section}

{'Link to user information'|i18n('design/standard/user/register')}:
<a href="{$userlink}">{$userlink}</a><br/><br/>


</body></html>
