<div class="page-title">
    <h1>{"Change password"|i18n("design/ezwebin/user/password")} </h1>
</div>

{* 
	login
	success	
	errors['oldPasswordNotValid']
 	errors['missingPassword']
	errors['newPasswordNotMatch']
	errors['newPasswordTooShort']
	errors['ldapConnectFail']
	errors['ldapLoginFail']
	errors['ldapLoginNotFound']
	errors['ldapManyLoginsFound']
	errors['ldapUnknownError']
*}


<h2>User {$login}</h2>

<div class="main subpage">
<div class="leftside">
	<div class="user-password">
		<form action={concat($module.functions.password.uri,"/",$userID)|ezurl} method="post" name="Password">
		{if $errors['ldapConnectFail']}
	    	<div class="warning"> <h2>Failed to connect to authentication service. Please contact Administrator</h2> </div>
	    {/if}
	    {if $errors['ldapLoginFail']}
	    	<div class="warning"> <h2>Please retype your old password.</h2> </div>
	    {/if}
	    {if $errors['ldapLoginNotFound']}
	    	<div class="warning"> <h2>User not found.</h2> </div>
	    {/if}	
	    {if $errors['ldapManyLoginsFound']}
	    	<div class="warning"> <h2>Multiple Logins Found. Please contact Administrator</h2> </div>
	    {/if}
	    {if $errors['ldapUnknownError']}
	    	<div class="warning"> <h2>Unknown: {$errors['ldapUnknownError']}</h2> </div>
	    {/if}
	    
	    {if $errors['oldPasswordNotValid']}
	        <div class="warning"> <h2>Please retype your old password.</h2> </div>
	    {/if}
	    {if $errors['newPasswordNotMatch']}
	        <div class="warning"> <h2>Password didn't match, please retype your new password.</h2> </div>
	    {/if}
	    {if $errors['newPasswordTooShort']}
	        <div class="warning"> <h2>{"The new password must be at least %1 characters long, please retype your new password."|i18n( 'design/ezwebin/user/password','',array( ezini('UserSettings','MinPasswordLength') ) )}</h2> </div>
	    {/if}	
		
		{if $success}
		    <div class="feedback">
		        <h2>Password successfully updated.</h2>
		    </div>
		{/if}
		
		<div class="block">
		{if $errors['oldPasswordNotValid']}*{/if}
		<label>{"Old password"|i18n("design/ezwebin/user/password")}</label><div class="labelbreak"></div>
		<input class="halfbox" type="password" name="password" size="11" value="" />
		</div>
		
		<div class="block">
		
			<div class="element">
			{if $errors['newPasswordNotMatch']}*{/if}
			<label>{"New password"|i18n("design/ezwebin/user/password")}</label><div class="labelbreak"></div>
			<input class="halfbox" type="password" name="newPassword" size="11" value="" />
			</div>
			
			<div class="element">
			{if $errors['newPasswordNotMatch']}*{/if}
			<label>{"Retype password"|i18n("design/ezwebin/user/password")}</label><div class="labelbreak"></div>
			<input class="halfbox" type="password" name="confirmPassword" size="11" value="" />
			</div>
			
			<div class="break"></div>
		</div>
		
		<div class="buttonblock">
		<input class="defaultbutton" type="submit" name="OKButton" value="{'OK'|i18n('design/ezwebin/user/password')}" />
		<input class="button" type="submit" name="CancelButton" value="{'Cancel'|i18n('design/ezwebin/user/password')}" />
		</div>
		
		</form>
	
	</div>
	
</div>
<div class="rightcol"></div>
</div>
