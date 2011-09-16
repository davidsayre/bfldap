{set-block scope=root variable=content_type}text/html{/set-block}
<html><body>
{let site_url=ezini("SiteSettings","SiteURL")}
{set-block scope=root variable=subject}{"%siteurl new password"|i18n("design/standard/user/forgotpassword",,hash('%siteurl',$site_url))}{/set-block}
{def $pwlink = concat("http://", $site_url, concat("user/forgotpassword/", $hash_key, "/")|ezurl(no))}

{"Your account information"|i18n('design/standard/user/forgotpassword')}<br/><br/>


{"Email"|i18n('design/standard/user/forgotpassword')}: {$user.email}<br/><br/>


{section show=$link}
{"Click here to get new password"|i18n('design/standard/user/forgotpassword')}:<br/>

<a href="{$pwlink}">{$pwlink}</a><br/></br/>
{section-else}


{section show=$password}
{"New password"|i18n('design/standard/user/forgotpassword')}: {$password}<br/><br/>
{/section}

{/section}

{/let}
</body></html>
