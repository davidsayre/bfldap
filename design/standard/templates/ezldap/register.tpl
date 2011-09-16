<div class="page-title">
    <h1>Register / Signup</h1>
</div>

{* 
	login
	email
	success
	firstname
	lastname
 	errors['missingLogin']
 	errors['missingEmail'] 	
 	errors['missingPassword']
 	errors['missingConfirmPassword']
	errors['missingFirstname']
 	errors['missingLastname']
	errors['ldapConnectFail']
	errors['ldapLoginFail']
	errors['ldapLoginNotFound']
	errors['ldapManyLoginsFound']
	errors['ldapUnknownError']
*}

{* TODO
REDIRECT user to success page
*}
<h2>New User Credentials</h2>

<div class="main subpage">
<div class="leftside">
	<div class="user-register">
		<form action={concat($module.functions.register.uri,"/")|ezurl} method="post" name="Register">
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
	    	<div class="warning"> <h2>{$errors['ldapUnknownError']}</h2> </div>
	    {/if}

	    {if $errors['missingLogin']}
	        <div class="warning"> <h2>Please retype your login.</h2> </div>
	    {/if}	    
	    {if $errors['missingEmail']}
	        <div class="warning"> <h2>Please retype your email.</h2> </div>
	    {/if}
	    {if $errors['missingPassword']}
	        <div class="warning"> <h2>Please retype your new password.</h2> </div>
	    {/if}
	    {if $errors['missingFirstname']}
	        <div class="warning"> <h2>Please retype your first name.</h2> </div>
	    {/if}
	    {if $errors['missingLastname']}
	        <div class="warning"> <h2>Please retype your last name.</h2> </div>
	    {/if}
	    		
		{if $success}
		    <div class="feedback">
		        <h2>User successfully created.</h2>
		    </div>
		{/if}
		
		<div class="block">
		{if $errors['missingLogin']}*{/if}
		<label>{"Login"|i18n("design/ezwebin/user/password")}</label><div class="labelbreak"></div>
		<input class="halfbox" type="text" name="login" size="50" value="{$login}" />
		</div>
		
		<div class="block">
		{if $errors['missingEmail']}*{/if}
		<label>{"Email"|i18n("design/ezwebin/user/password")}</label><div class="labelbreak"></div>
		<input class="halfbox" type="text" name="email" size="50" value="{$email}" />
		</div>
		
		<div class="block">
		{if $errors['missingPassword']}*{/if}
		<label>{"Password"|i18n("design/ezwebin/user/password")}</label><div class="labelbreak"></div>
		<input class="halfbox" type="password" name="password" size="11" value="" />
		</div>
		
		<div class="block">
			{if or($errors['missingConfirmPassword'],$errors['newPasswordNotMatch'])}*{/if}
			<label>{"Retype password"|i18n("design/ezwebin/user/password")}</label><div class="labelbreak"></div>
			<input class="halfbox" type="password" name="confirmPassword" size="11" value="" />
		</div>
		
		<div class="block">
		{if $errors['missingFirstname']}*{/if}
		<label>{"First Name"|i18n("design/ezwebin/user/password")}</label><div class="labelbreak"></div>
		<input class="halfbox" type="text" name="firstname" size="50" value="{$firstname}" />
		</div>
		
		<div class="block">
		{if $errors['missingLastname']}*{/if}
		<label>{"Last Name"|i18n("design/ezwebin/user/password")}</label><div class="labelbreak"></div>
		<input class="halfbox" type="text" name="lastname" size="50" value="{$lastname}" />
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
