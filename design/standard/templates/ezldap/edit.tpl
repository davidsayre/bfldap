


<div class="page-title">
  <h1>{"User profile"|i18n("design/ezwebin/user/edit")}</h1>
</div>

<div class="main subpage" id="full-article">

<form action={concat($module.functions.edit.uri,"/",$userID)|ezurl} method="post" name="Edit">

<div class="leftside section-details user-edit">

     <h2>{$userAccount.contentobject.name|wash}</h2>
		{def $attribute = ''
             $field_filter=array()}

			{set $field_filter=array('user_account')}
            <div class="formsec info">

                <h3>Login information:</h3>

                <div class="user_account">
                <div class="block user_email">
                  <label>{"Email"|i18n("design/ezwebin/user/edit")}:</label> <span class="box">{$userAccount.email|wash(email)}</span>
                </div>

                <div class="block user_username">
                  <label>{"Username"|i18n("design/ezwebin/user/edit")}:</label> <span class="box">{$userAccount.login|wash}</span>
                </div>
                </div>
                
                
            </div>

            {set $field_filter=array('first_name','last_name','organization','zip_code', 'country', 'subjects', 'other_interests')}

            <div class="formsec about">

                <h3>About you:</h3>

                {foreach $field_filter as $attrname}
                    {set $attribute = $userAccount.contentobject.data_map.$attrname}
                    <div class="block {$attribute.contentclass_attribute_identifier}">
                        <label class="attr_label">{$attribute.contentclass_attribute.name}:</label><div class="labelbreak"></div>
                        {attribute_view_gui attribute=$attribute}
                    </div>
                {/foreach}
        
            </div>

            {set $field_filter=array('send_gift_card','mail_address','mail_city','mail_state','mail_zip_code','send_gs_updates','send_fc_updates')}

            <div class="formsec subscribe">

  	        {foreach $field_filter as $attrname}
                {set $attribute = $userAccount.contentobject.data_map.$attrname}
			    <div class="block {$attribute.contentclass_attribute_identifier}">
			        <label class="attr_label">{$attribute.contentclass_attribute.name}:</label><div class="labelbreak"></div>
			        {attribute_view_gui attribute=$attribute}
			    </div>
	        {/foreach}

            </div>

<div class="buttonblock">
<input class="button" type="submit" name="EditButton" value="{'Edit profile'|i18n('design/ezwebin/user/edit')}" />
<input class="button" type="submit" name="ChangePasswordButton" value="{'Change password'|i18n('design/ezwebin/user/edit')}" />
</div>

</div>

<div class="rightcol"></div>

</form>

</div>



