<?php /*

[LDAPSettings]
LDAPUserClasses[]
LDAPUserClasses[]=inetorgperson
LDAPUserClasses[]=organizationalPerson
LDAPUserClasses[]=person
LDAPUserClasses[]=top

#Passwod attribute
LDAPPasswordAttribute=userPassword

#Create users in this DN
LDAPUserCreateDN=ou--People,dc--beaconfire,dc--us

#Default group placement (siteaccess may overwride) 
LDAPUserCreateDefaultGroup=webVisitors

# LDAP attribute for UID. 389-DS = nsUniqueID, AD = objectID
LDAPGUIDAttribute=uidNumber

# LDAP attribute for GUID. 389-DS = userPassword, AD = ??
LDAPPasswordAttribute=userPassword

#Location of new groups
LDAPGroupCreateDN=ou--Web,ou--Groups,dc--beaconfire,dc--us
LDAPGroupCreatePrefix=web
LDAPUserCreateBindDN=cn--Directory Manager
LDAPUserCreateBindPassword=password
*/ ?>
