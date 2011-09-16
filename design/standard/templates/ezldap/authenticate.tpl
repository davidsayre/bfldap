<div class="page-title">
    <h1>"Authenticate" {$userAccount.login}</h1>
</div>

{* @param UserID taken from currentUser() *}
<div class="main subpage">
<div class="leftside">
	<div class="user-password">
		<form action="" method="post" name="Authenticate">
	
		{if $errors['PasswordNotValid']}
	        <div class="warning"> <h2>Please retype your old password.</h2> </div>
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
	    {if $errors['ldapConnectFail']}
	    	<div class="warning"> <h2>Failed to connect to authentication service. Please contact Administrator</h2> </div>
	    {/if}	
				
		{if $success}
		    <div class="feedback">
		        <h2>Login Successfull.</h2>
		    </div>
		{/if}
				
		<div class="block">
		{if $PasswordNotValid}*{/if}
		<label>Password</label><div class="labelbreak"></div>
		<input class="halfbox" type="password" name="Password" size="11" value="{$Password}" />
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
