<?php 

/* LDAP write 
 * 
 * see kernel/classes/datatypes/ezuser/eZLDAPWrite.php
 * see kernel/user/password.php
 * new LDAPGUIDAttribute in ldap.ini.append.php - used to bind ezuser.remote_id = ldapGUID
 * 
 * 
 */

class ezLDAPWrite extends eZUser 
{
	const NOT_DEFINED = -1;
 	function ezLDAPWrite( )
    {
     
    }
    
    static function initLDAP() {    	
    	
        $LDAPIni = eZINI::instance( 'ldap.ini' );
        $LDAP = Array();
        
    	if ($LDAPIni->variable( 'LDAPSettings', 'LDAPEnabled' ) === 'true' )
        {
            // read LDAP ini settings
            $iniVars = $LDAPIni->getNamedArray(); //all keys             
            			
            $LDAP['DebugTrace']         = $LDAPIni->variable( 'LDAPSettings', 'LDAPDebugTrace' ) === 'enabled';
            $LDAP['Version']            = $LDAPIni->variable( 'LDAPSettings', 'LDAPVersion' );
            $LDAP['Server']             = $LDAPIni->variable( 'LDAPSettings', 'LDAPServer' );
            $LDAP['Port']               = $LDAPIni->variable( 'LDAPSettings', 'LDAPPort' );
            $LDAP['FollowReferrals']    = (int) $LDAPIni->variable( 'LDAPSettings', 'LDAPFollowReferrals' );
            $LDAP['BaseDN']             = $LDAPIni->variable( 'LDAPSettings', 'LDAPBaseDn' );
            $LDAP['BindUser']          = $LDAPIni->variable( 'LDAPSettings', 'LDAPBindUser' );
            $LDAP['BindPassword']      = $LDAPIni->variable( 'LDAPSettings', 'LDAPBindPassword' );
            $LDAP['SearchScope']        = $LDAPIni->variable( 'LDAPSettings', 'LDAPSearchScope' );

            $LDAP['LoginAttribute']     = $LDAPIni->variable( 'LDAPSettings', 'LDAPLoginAttribute' );
            $LDAP['FirstNameAttribute'] = $LDAPIni->variable( 'LDAPSettings', 'LDAPFirstNameAttribute' );
            $LDAP['FirstNameIsCN']      = $LDAPIni->variable( 'LDAPSettings', 'LDAPFirstNameIsCommonName' ) === 'true';
            $LDAP['LastNameAttribute']  = $LDAPIni->variable( 'LDAPSettings', 'LDAPLastNameAttribute' );
            $LDAP['EmailAttribute']     = $LDAPIni->variable( 'LDAPSettings', 'LDAPEmailAttribute' );
			
            $LDAP['defaultUserPlacement']  = $LDAPIni->variable( "UserSettings", "DefaultUserPlacement" );

            $LDAP['UserGroupAttributeType'] = $LDAPIni->variable( 'LDAPSettings', 'LDAPUserGroupAttributeType' );
            $LDAP['UserGroupAttribute']     = $LDAPIni->variable( 'LDAPSettings', 'LDAPUserGroupAttribute' );

            /* 
             * New variables
             */ 
            $LDAP['PasswordAttribute']  = $LDAPIni->variable( 'LDAPSettings', 'LDAPPasswordAttribute' );
			$LDAP['GUIDAttribute']  = $LDAPIni->variable( 'LDAPSettings', 'LDAPGUIDAttribute' );
			$LDAP['UserClasses']  = $LDAPIni->variable( 'LDAPSettings', 'LDAPUserClasses' ); //ARRAY
			$LDAP['UserCreateDN']  = $LDAPIni->variable( 'LDAPSettings', 'LDAPUserCreateDN' ); //fix encoding below
			$LDAP['UserCreateDefaultGroup']  = $LDAPIni->variable( 'LDAPSettings', 'LDAPUserCreateDefaultGroup' );
			$LDAP['GroupCreateDN']  = $LDAPIni->variable( 'LDAPSettings', 'LDAPGroupCreateDN' ); //fix encoding below
			$LDAP['GroupCreatePrefix']  = $LDAPIni->variable( 'LDAPSettings', 'LDAPGroupCreatePrefix' );  //fix encoding below
			$LDAP['UserCreateBindDN']  = $LDAPIni->variable( 'LDAPSettings', 'LDAPUserCreateBindDN' );  //fix encoding below
			$LDAP['UserCreateBindPassword']  = $LDAPIni->variable( 'LDAPSettings', 'LDAPUserCreateBindPassword' );  //fix encoding below			
			
            if ( $LDAPIni->hasVariable( 'LDAPSettings', 'Utf8Encoding' ) )
            {
			$Utf8Encoding = $LDAPIni->variable( 'LDAPSettings', 'Utf8Encoding' );
			if ( $Utf8Encoding == "true" )
			    $LDAP['$isUtf8Encoding'] = true;
			else
			    $LDAP['$isUtf8Encoding'] = false;
            }
            else
            {
			$LDAP['$isUtf8Encoding'] = false;
            }

            if ( $LDAPIni->hasVariable( 'LDAPSettings', 'LDAPSearchFilters' ) )
            {
                $LDAP['Filters'] = $LDAPIni->variable( 'LDAPSettings', 'LDAPSearchFilters' );
            }
            if ( $LDAPIni->hasVariable( 'LDAPSettings', 'LDAPUserGroupType' ) and  $LDAPIni->hasVariable( 'LDAPSettings', 'LDAPUserGroup' ) )
            {
                $LDAP['UserGroupType'] = $LDAPIni->variable( 'LDAPSettings', 'LDAPUserGroupType' );
                $LDAP['UserGroup'] = $LDAPIni->variable( 'LDAPSettings', 'LDAPUserGroup' );
            }

            $LDAP['Filter'] = "( &";
            if ( count( $LDAP['Filters'] ) > 0 )
            {
                foreach ( array_keys( $LDAP['Filters'] ) as $key )
                {
                    $LDAP['Filter'] .= "(" . $LDAP['Filters'][$key] . ")";
                }
            }
            //FIX encoding
            $LDAP['EqualSign'] = trim($LDAPIni->variable( 'LDAPSettings', "LDAPEqualSign" ) );
            foreach($LDAP as $key => $value) {            	
            	if ( ($key != 'EqualSign') && (is_string($LDAP[$key])) && (strlen($LDAP[$key]) > 0) && (strpos($LDAP[$key],$LDAP['EqualSign']) > 0) ) {
					$LDAP[$key]    = str_replace( $LDAP['EqualSign'], "=", $LDAP[$key] );
            	}
            }
            
            return $LDAP;
        } else {
        	return false;
        }
			 
    }
    
   /**
	 * Wrapper for LDAP authentication
     *
	 * @param $login (string)
	 * @param $password (string)
	 * @param $authenticationMatch (boolean)
	 * @return (array)
	 */
	public function authenticate($login, $password, $authenticationMatch = false  )
    {
    	
    	$messages = array();
        $messages['success'] = 0;
        $messages['errors'] = Array();
        
    	$LDAP = self::initLDAP(); 	
    	// check settings
    	if(!$LDAP) {
    		eZDebug::writeNotice('LDAP.ini failed to load' , __METHOD__ );
    		$messages['errors']['ldapSettingsNotFound'] = 1;
			return $messages;
    	}    	

		//if ( $authenticationMatch === false ) $authenticationMatch = eZUser::authenticationMatch();
		//$db = eZDB::instance();
        //$loginEscaped = $db->escapeString( $login );
        //$passwordEscaped = $db->escapeString( $password );
    	 
        //check params
		if( !isset( $login ) ) $login = '';
		if( !isset( $password ) ) $password = '';
		   		
		if ( function_exists( 'ldap_connect' ) ) 
		{
			$ds = ldap_connect( $LDAP['Server'], $LDAP['Port'] );
		} else {
			$messages['errors']['ldapConnectFail'] = 1;
			return $messages;
			$ds = false; //failsafe
		}
		
		if ( $LDAP['DebugTrace'] )
		{
			$debugArray =  array( 'stage' => '1/4: Connecting and Binding to LDAP server','LDAPServer' => $LDAP['Server'],'LDAPPort' => $LDAP['Port'],'LDAPBindUser' => $LDAP['BindUser'],'LDAPVersion' => $LDAP['Version']
			);
			// Set debug trace mode for ldap connections
			if ( function_exists( 'ldap_set_option' ) ) {
			    ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
				eZDebug::writeNotice( var_export( $debugArray, true ), __METHOD__ );
			}
		}
		
		if ( $ds )
		{
			ldap_set_option( $ds, LDAP_OPT_PROTOCOL_VERSION, $LDAP['Version'] );
			ldap_set_option( $ds, LDAP_OPT_REFERRALS, $LDAP['FollowReferrals'] );

			//bind anonymous, or as user to fetch user's DN
			if ( $LDAP['BindUser'] == '' )
			{
			    $r = ldap_bind( $ds );
			}
			else
			{
			    $r = ldap_bind( $ds, $LDAP['BindUser'], $LDAP['BindPassword'] );
			}
			
			if ( !$r )
			{
			    eZDebug::writeError( 'Cannot bind to LDAP server, might be something wrong with connetion or bind user!', __METHOD__ );
			    $messages['errors']['ldapConnectFail'] = 1;
			    // Increase number of failed login attempts.
			    //if ( isset( $userID ) )
			    	//eZUser::setFailedLoginAttempts( $userID );
				ldap_close( $ds );
			    return $messages;
			}			
		
			$LDAP['Filter'] .= "(" .$LDAP['LoginAttribute']. "=" .$login. "))";

			//ldap_set_option( $ds, LDAP_OPT_SIZELIMIT, 0 );
			//ldap_set_option( $ds, LDAP_OPT_TIMELIMIT, 0 );

			//DJS added GUID
			$retrieveAttributes = array( $LDAP['LoginAttribute'],
						             $LDAP['FirstNameAttribute'],
						             $LDAP['LastNameAttribute'],
						             $LDAP['EmailAttribute'],
						             $LDAP['GUIDAttribute']
						              );
						             
			if ( $LDAP['UserGroupAttributeType'] )
			    $retrieveAttributes[] = $LDAP['UserGroupAttribute'];

			if ( $LDAP['DebugTrace'] )
			{
			    $debugArray = array( 'stage' => '2/4: finding user',
						         'LDAPFilter' => $LDAP['Filter'],
						         'retrieveAttributes' => $retrieveAttributes,
						         'LDAPSearchScope' => $LDAP['SearchScope'],
						         'LDAPBaseDN' => $LDAP['BaseDN']
			    );
			    eZDebug::writeNotice( var_export( $debugArray, true ), __METHOD__ );
			}

			if ( $LDAP['SearchScope'] == "one" )
			    $sr = ldap_list( $ds, $LDAP['BaseDN'], $LDAP['Filter'], $retrieveAttributes );
			else if ( $LDAP['SearchScope'] == "base" )
			    $sr = ldap_read( $ds, $LDAP['BaseDN'], $LDAP['Filter'], $retrieveAttributes );
			else
			    $sr = ldap_search( $ds, $LDAP['BaseDN'], $LDAP['Filter'], $retrieveAttributes );

			//fetch records from ldap
			$info = ldap_get_entries( $ds, $sr ) ;
				
			if ( $info['count'] > 1 )
			{
			    // More than one user with same uid, not allow login.
			    eZDebug::writeWarning( 'More then one user with same uid, not allowed to login!', __METHOD__ );
			    $messages['errors']['ldapManyLoginsFound'] = 1 ;
			    ldap_close( $ds );
           		return $messages;
			}
			else if ( $info['count'] < 1 )
			{
			    // Increase number of failed login attempts.
			    if ( isset( $userID ) )
			       // eZUser::setFailedLoginAttempts( $userID );
			    // user DN was not found
			    eZDebug::writeWarning( 'User DN was not found!', __METHOD__ );
			    $messages['errors']['ldapLoginNotFound'] = 1 ;
			    ldap_close( $ds );
            	return $messages;
			}
			else if ( $LDAP['DebugTrace'] )
			{
			    $debugArray = array( 'stage' => '3/4: real authentication of user',
					'info' => $info
			    );
			    eZDebug::writeNotice( var_export( $debugArray, true ), __METHOD__ );
			}
						
            // is it real authenticated LDAP user?
            if ( @ldap_bind( $ds, $info[0]['dn'], $password ) )
            {
            	if ( $LDAP['DebugTrace'] ) {
            		$debugArray = array( 'stage' => '4/4: bind with password success! ');
					eZDebug::writeNotice( var_export( $debugArray, true ), __METHOD__ );
            	}
				$messages['success'] = 1 ;
            	ldap_close( $ds );
				return $messages;
				//exit success
            } else {
				eZDebug::writeWarning( "User failed to login!", __METHOD__ );
				$messages['errors']['ldapLoginFail'] = 1 ;
            	ldap_close( $ds );
				return $messages;
			}
        
            ldap_close( $ds );
		} else {
			eZDebug::writeError( 'Cannot initialize connection for LDAP server', __METHOD__ );
			return $messages;
		} // END if (ds)	

		//failsafe
		return $messages;
    }
    
     /*
     * Wrapper for LDAP change password on self
     * 
     * @param $login (string) required     
     * @param $password (string) required
     * @param $confirmPassword (string) required   
     * @return (array)  
     */
    public function changePassword($login, $password, $newPassword, $confirmPassword) {

    	/*
    	 * Authenticate (login,password)
    	 * retreive current LDAP user by 
    	 * bind with password
    	 * Write password back to LDAP
	   	 * Provide messages back to user
    	 */
    	
    	$messages = array();
        $messages['success'] = 0;
        $messages['errors'] = Array();
         	        
        $siteINI = eZINI::instance(); //min password length
        $minPasswordLength = $siteINI->hasVariable( 'UserSettings', 'MinPasswordLength' ) ? $siteINI->variable( 'UserSettings', 'MinPasswordLength' ) : 3;
                
        $LDAP = self::initLDAP(); 	
    	// check settings
    	if(!$LDAP) {
    		eZDebug::writeNotice('LDAP.ini failed to load' , __METHOD__ );
    		$messages['errors']['ldapSettingsNotFound'] = 1;
			return $messages;
    	}    	

		//if ( $authenticationMatch === false ) $authenticationMatch = eZUser::authenticationMatch();
		//$db = eZDB::instance();
        //$loginEscaped = $db->escapeString( $login );
        //$passwordEscaped = $db->escapeString( $password );
    	  	
   		//check params
		if( !isset( $login ) ) $login = '';
		if( !isset( $password ) ) $password = '';		
		if( !isset( $newPassword ) ) $newPassword = '';		
		if( !isset( $confirmPassword ) )  $confirmPassword = '';		    
		
		//check input    
		if (  $newPassword !== $confirmPassword )        
        {
        	eZDebug::writeError( 'Password not confirmed', __METHOD__ );
        	$messages['errors']['newPasswordNotMatch'] = 1;
            return $messages;
        }
                
        if ( strlen( $newPassword ) < $minPasswordLength )
        {
        	eZDebug::writeNotice( 'Password to short', __METHOD__ );
            $messages['errors']['newPasswordTooShort'] = 1 ;
            return $messages;
            
        }         	

    	
        if ( function_exists( 'ldap_connect' ) ) 
        {
			$ds = ldap_connect( $LDAP['Server'], $LDAP['Port'] );			    
		} else {
			eZDebug::writeNotice( 'Unable to connect to LDAP.', __METHOD__ );
			$messages['errors']['ldapConnectFail'] = 1;
			return $messages;
			$ds = false;				
		}			
		
		if ( $LDAP['DebugTrace'] )
		{
			$debugArray =  array( 'stage' => '1/4: Connecting and Binding to LDAP server','LDAPServer' => $LDAP['Server'],'LDAPPort' => $LDAP['Port'],'LDAPBindUser' => $LDAP['BindUser'],'LDAPVersion' => $LDAP['Version']
			);
			// Set debug trace mode for ldap connections
			if ( function_exists( 'ldap_set_option' ) ) {
			    ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
				eZDebug::writeNotice( var_export( $debugArray, true ), __METHOD__ );
			}
		}

		
		//ds created
		if ( $ds )
		{
			ldap_set_option( $ds, LDAP_OPT_PROTOCOL_VERSION, $LDAP['Version'] );
			ldap_set_option( $ds, LDAP_OPT_REFERRALS, $LDAP['FollowReferrals'] );
			$r = ldap_bind( $ds, $LDAP['BindUser'], $LDAP['BindPassword'] );
							
			if ( !$r )
			{
			    eZDebug::writeError( 'Cannot bind to LDAP server, might be something wrong with connection or bind user!', __METHOD__ );			    
				$messages['errors']['ldapBindFail'] = 1;
				ldap_close( $ds );
				return $messages;
			}
		
			if (count($messages['errors']) == 0) {
				$LDAP['Filter'] .= "(" .$LDAP['LoginAttribute']. "=" .$login. "))";
		
				//ldap_set_option( $ds, LDAP_OPT_SIZELIMIT, 0 );
				//ldap_set_option( $ds, LDAP_OPT_TIMELIMIT, 0 );
		
				//DJS added GUID
				$retrieveAttributes = array( $LDAP['LoginAttribute'],
							             $LDAP['FirstNameAttribute'],
							             $LDAP['LastNameAttribute'],
							             $LDAP['EmailAttribute'],
							             $LDAP['GUIDAttribute']
							              );
							             
				if ( $LDAP['UserGroupAttributeType'] ) $retrieveAttributes[] = $LDAP['UserGroupAttribute'];
					 eZDebug::writeNotice( var_export( $retrieveAttributes, true ), __METHOD__ );
				 
				if ( $LDAP['SearchScope'] == "one" ) {
				    $sr = ldap_list( $ds, $LDAP['BaseDN'], $LDAP['Filter'], $retrieveAttributes );
				} else if ( $LDAP['SearchScope'] == "base" ) {
				    $sr = ldap_read( $ds, $LDAP['BaseDN'], $LDAP['Filter'], $retrieveAttributes );
				} else {
				    $sr = ldap_search( $ds, $LDAP['BaseDN'], $LDAP['Filter'], $retrieveAttributes );
				}
				
				//fetch records from ldap
				$info = ldap_get_entries( $ds, $sr ) ;
				
				if ( $info['count'] > 1 )
				{
				    // More than one user with same uid, not allow.
				    eZDebug::writeWarning( 'More then one user with same uid', __METHOD__ );
				   	$messages['errors']['ldapManyLoginsFound'] = 1 ;
					ldap_close( $ds );
				   	return $messages;
				}
				else if ( $info['count'] < 1 )
				{					    
				    eZDebug::writeWarning( 'User DN was not found!', __METHOD__ );
				    $messages['errors']['ldapLoginNotFound'] = 1 ;
				    ldap_close( $ds );
					return $messages;
				}
				else if ( $LDAP['DebugTrace'] )
				{
				    $debugArray = array( 'stage' => '2/4: real authentication of user',
						'info' => $info
				    );
				    eZDebug::writeNotice( var_export( $debugArray, true ), __METHOD__ );
				}
			}
	        // is it real authenticated LDAP user?
			if ( @ldap_bind( $ds, $info[0]['dn'], $password ) )
	        {
					
	        	if ( $LDAP['DebugTrace'] ) {
					$debugArray = array( 'stage' => '3/4: bind with password success! ');
					eZDebug::writeNotice( var_export( $debugArray, true ), __METHOD__ );
	        	}
					
				//get user's DN
				$user_DSN = $info[0]['dn'];
					
				//change password
				$new_attributes = array('userPassword'=>$newPassword);
				$r = ldap_modify($ds, $user_DSN, $new_attributes);
				
				//unknown error check
				$r_error = ldap_error($ds);
				if($r_error != 'Success') {
					eZDebug::writeError( $r_error, __METHOD__ );
					$messages['errors']['ldapUnknownError'] = $r_error;					
					ldap_close( $ds );
					return $messages;				 
				}
							
				$debugArray = array( 'stage' => '4/4: Password updated! ');
				eZDebug::writeNotice( var_export( $debugArray, true ), __METHOD__ );
				
				$messages['success'] = 1;
				ldap_close( $ds );				
				return $messages;
					
	        } else {
				eZDebug::writeWarning( "User failed to login [" .$password. "[]", __METHOD__ );
				$messages['errors']['ldapLoginFail'] = 1 ;
				ldap_close( $ds );
				return $messages;        	
			}
			
			//unknown error check
			$r_error = ldap_error($ds);
			if($r_error != 'Success') {
				eZDebug::writeError( $r_error, __METHOD__ );
				$messages['errors']['ldapUnknownError'] = $r_error;
				ldap_close( $ds );
				return $messages;
			}
						
	       	ldap_close( $ds );	//failsafe       	
            
		} else {
			eZDebug::writeError( 'Cannot initialize connection for LDAP server', __METHOD__ );
			$messages['errors']['ldapConnectFail'] = 1 ;
			return $messages;
		} // END if (ds)	
        
    	return $messages; //failsafe
    }

    /*
     * Wrapper for LDAP add user (class and fields)
     * 
     * @param $login (string) required
     * @param $email (string) required
     * @param $password (string) required
     * @param $confirmPassword (string) required
     * @param firstname (string) required
     * @param $lastname (string) required
     * 
     * @return (array)
     */
    public function createUser($login, $email, $password, $confirmPassword, $firstname, $lastname){

    	$messages = array();
        $messages['success'] = 0;
        $messages['errors'] = Array();

		$siteINI = eZINI::instance(); //min password length
        $minPasswordLength = $siteINI->hasVariable( 'UserSettings', 'MinPasswordLength' ) ? $siteINI->variable( 'UserSettings', 'MinPasswordLength' ) : 3;
        
    	$LDAP = self::initLDAP();    

    	// check settings
    	if(!$LDAP) {
    		eZDebug::writeNotice('LDAP.ini failed to load' , __METHOD__ );
    		$messages['errors']['ldapSettingsNotFound'] = 1 ;
    		return $messages;
    	}
		
    	//check params
		if( !isset( $login ) ) $login = '';
		if( !isset( $email ) ) $email = '';
		if( !isset( $password ) ) $password = '';
		if( !isset( $confirmPassword ) ) $confirmPassword = '';
		if( !isset( $firstname ) ) $firstname = '';
		if( !isset( $lastname ) ) $lastname = '';
		    
		//check input 
		if (strlen($login) == 0 ) { $messages['errors']['missingLogin'] = 1 ; return $messages; }
    	if (strlen($email) == 0 ) { $messages['errors']['missingEmail'] = 1 ; return $messages; }
    	if (strlen($password) == 0 ) { $messages['errors']['missingPassword'] = 1 ; return $messages; }
   		if (strlen($confirmPassword) == 0 ) { $messages['errors']['missingConfirmPassword'] = 1 ; return $messages; }
    	if (strlen($firstname) == 0 ) { $messages['errors']['missingFirstname'] = 1 ; return $messages; }
    	if (strlen($lastname) == 0 ) { $messages['errors']['missingLastname'] = 1 ; return $messages; }

    	if (  $password !== $confirmPassword )        
        {
        	eZDebug::writeError( 'Password not confirmed', __METHOD__ );
        	$messages['errors']['newPasswordNotConfirmed'] = 1;
            return $messages;
        }
                
        if ( strlen( $password ) < $minPasswordLength )
        {
        	eZDebug::writeNotice( 'Password to short', __METHOD__ );
            $messages['errors']['newPasswordTooShort'] = 1 ;
            return $messages;
        }
			
		if ( function_exists( 'ldap_connect' ) ) 
        {
			$ds = ldap_connect( $LDAP['Server'], $LDAP['Port'] );
		} else {
			eZDebug::writeError( 'Unable to connect to LDAP.', __METHOD__ );
			$messages['errors']['ldapConnectFail'] = 1;
			return $messages;
			$ds = false; //failsafe	
		}
		
    	if ( $LDAP['DebugTrace'] )
		{
			$debugArray =  array( 'stage' => '1/3: Connecting and Binding to LDAP server','LDAPServer' => $LDAP['Server'],'LDAPPort' => $LDAP['Port'],'LDAPBindUser' => $LDAP['BindUser'],'LDAPVersion' => $LDAP['Version']
			);
			// Set debug trace mode for ldap connections
			if ( function_exists( 'ldap_set_option' ) ) {
			    ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
				eZDebug::writeNotice( var_export( $debugArray, true ), __METHOD__ );
			}
		}
			
		if ( $ds )
		{
			ldap_set_option( $ds, LDAP_OPT_PROTOCOL_VERSION, $LDAP['Version'] );
			ldap_set_option( $ds, LDAP_OPT_REFERRALS, $LDAP['FollowReferrals'] );
			$r = ldap_bind( $ds, $LDAP['UserCreateBindDN'], $LDAP['UserCreateBindPassword'] );
			
			if ( !$r )
			{				 				    
			    eZDebug::writeError( 'Cannot bind to LDAP server, might be something wrong with connection or bind user!', __METHOD__ );
				$messages['errors']['ldapBindFail'] = 1;
				ldap_close( $ds );
				return $messages;
			}
			
			if ( $LDAP['DebugTrace'] )
			{ 
				$debugArray = array( 'stage' => '2/3: Bind successful','info' => $info );
				eZDebug::writeNotice( var_export( $debugArray, true ), __METHOD__ );				
			}

       		//set User's parent DN
			$user_parentDN = $LDAP['UserCreateDN'];
			//set user's DN
			$user_DN = 'cn='. $login . ',' .$user_parentDN;
				
			//create user
			$new_attributes['uid']=$login; // fully qualified? or just new
			$new_attributes[$LDAP['LoginAttribute']] = $login;
			$new_attributes[$LDAP['EmailAttribute']] = $email;
			$new_attributes[$LDAP['PasswordAttribute']] = $password; //plain text ??
			if (strlen($firstname)) {
				$new_attributes[$LDAP['FirstNameAttribute']] = $firstname;
			}
			if (strlen($lastname)) {
				$new_attributes[$LDAP['LastNameAttribute']] = $lastname;
			}			
			$new_attributes['objectClass'] = $LDAP['UserClasses']; // ex. $new_attributes['objectClass'][0] = "top";
			
			//create entry
			$r = ldap_add( $ds, $user_DN, $new_attributes);
		
			//unknown error check
			$r_error = ldap_error($ds);
			if($r_error != 'Success') {
				eZDebug::writeError( var_export( $new_attributes, true ) . var_export($user_DN, true) . $r_error, __METHOD__ );
				$messages['errors']['ldapUnknownError'] = $r_error;
				ldap_close( $ds );
				return $messages;
			}
			
			if ( $LDAP['DebugTrace'] ) {
				$debugArray = array( 'stage' => '3/3: User Created! ');
				eZDebug::writeNotice( var_export( $debugArray, true ), __METHOD__ );
			}
			
			if ( $LDAP['SearchScope'] == "one" )
			    $sr = ldap_list( $ds, $LDAP['BaseDN'], $LDAP['Filter'], $retrieveAttributes );
			else if ( $LDAP['SearchScope'] == "base" )
			    $sr = ldap_read( $ds, $LDAP['BaseDN'], $LDAP['Filter'], $retrieveAttributes );
			else
			    $sr = ldap_search( $ds, $LDAP['BaseDN'], $LDAP['Filter'], $retrieveAttributes );

			//fetch records from ldap
			$info = ldap_get_entries( $ds, $sr ) ;
			
			$message['user'] = 
			
			$messages['success'] = 1;	
	       	ldap_close( $ds );						
			return $messages;
            
		} else {
			eZDebug::writeError( 'Cannot initialize connection for LDAP server', __METHOD__ );
			$messages['errors']['ldapConnectFail'] = 1 ;
			return $messages;
		} // END if ds         
    	return $messages; //failsafe
    }
    
    /*
     * GetUser - retrieve user using 'read' bind 
     * $param $login (string)
     * 
     * This is compacted and without debug.
     */
    public function getUser($login) {
    
    	$messages = array();
        $messages['success'] = 0;
        $messages['errors'] = Array();
        
    	$LDAP = self::initLDAP(); 	
    	// check settings
    	if(!$LDAP) { return false; }   	
    	 
        //check params
		if( !isset( $login ) ) $login = '';
		   		
		if ( function_exists( 'ldap_connect' ) ) 
		{
			$ds = ldap_connect( $LDAP['Server'], $LDAP['Port'] );
		} else {
			eZDebug::writeError( 'Unable to connect to LDAP.', __METHOD__ );
			return false;
		}
		
		if ( $ds )
		{
			ldap_set_option( $ds, LDAP_OPT_PROTOCOL_VERSION, $LDAP['Version'] );
			ldap_set_option( $ds, LDAP_OPT_REFERRALS, $LDAP['FollowReferrals'] );

			//bind anonymous, or as user to fetch user's DN
			if ( $LDAP['BindUser'] == '' )
			{
			    $r = ldap_bind( $ds );
			}
			else
			{
			    $r = ldap_bind( $ds, $LDAP['BindUser'], $LDAP['BindPassword'] );
			}
			
			if ( !$r )
			{
			    eZDebug::writeError( 'Cannot bind to LDAP server, might be something wrong with connetion or bind user!', __METHOD__ );		
				ldap_close( $ds );
			    return false;
			}			
		
			$LDAP['Filter'] .= "(" .$LDAP['LoginAttribute']. "=" .$login. "))";

			//ldap_set_option( $ds, LDAP_OPT_SIZELIMIT, 0 );
			//ldap_set_option( $ds, LDAP_OPT_TIMELIMIT, 0 );

			$retrieveAttributes = array( $LDAP['LoginAttribute'],
						             $LDAP['FirstNameAttribute'],
						             $LDAP['LastNameAttribute'],
						             $LDAP['EmailAttribute'],
						             $LDAP['GUIDAttribute']
						              );
						              						             
			if ( $LDAP['UserGroupAttributeType'] )
			    $retrieveAttributes[] = $LDAP['UserGroupAttribute'];
			if ( $LDAP['SearchScope'] == "one" )
			    $sr = ldap_list( $ds, $LDAP['BaseDN'], $LDAP['Filter'], $retrieveAttributes );
			else if ( $LDAP['SearchScope'] == "base" )
			    $sr = ldap_read( $ds, $LDAP['BaseDN'], $LDAP['Filter'], $retrieveAttributes );
			else
			    $sr = ldap_search( $ds, $LDAP['BaseDN'], $LDAP['Filter'], $retrieveAttributes );

			//fetch records from ldap
			$info = ldap_get_entries( $ds, $sr ) ;
				
			if ( $info['count'] > 1 )
			{
			    // More than one user with same uid, not allow login.
			    eZDebug::writeWarning( 'More then one user with same uid, not allowed to login!', __METHOD__ );
			    ldap_close( $ds );
           		return $messages;
			}
			else if ( $info['count'] < 1 )
			{
			    // Increase number of failed login attempts.
			    if ( isset( $userID ) )
			       // eZUser::setFailedLoginAttempts( $userID );
			    // user DN was not found
			    eZDebug::writeWarning( 'User DN was not found!', __METHOD__ );
			    $messages['errors']['ldapLoginNotFound'] = 1 ;
			    ldap_close( $ds );
            	return $messages;
			}
			else if ( $LDAP['DebugTrace'] )
			{
			    $debugArray = array( 'stage' => '3/4: real authentication of user',
					'info' => $info
			    );
			    eZDebug::writeNotice( var_export( $debugArray, true ), __METHOD__ );
			}
						
            // is it real authenticated LDAP user?
            if ( @ldap_bind( $ds, $info[0]['dn'], $password ) )
            {
    
			}
        
            ldap_close( $ds );
		} // END if (ds)	

		//failsafe
		return $user;
    }
    
    /*
     * eZ Check user exists
     * 
     * @param $login - unique login
     * @param $email - email address
     * @return (boolean) - True for user exists
     */
    
    private function checkEZUserExist($login,$email) {    	

    	$db = eZDB::instance();
		$db->begin();
	
	    $sql = "SELECT count(*) as count FROM ezuser WHERE login = $login or email = $email";
	    $rows = $db->arrayQuery( $sql );
	    $count = $rows[0]['count'];
	    if ( $count < 1 )
	    {
			return 1;
	    }
	   	return 0;
    }
    
    
}


?>