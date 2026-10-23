<?php
// Fuente solamente: no instalar antes de verificar la cuenta técnica y su membresía.
if( !defined( 'MANTIS_VERSION' ) ) {
	http_response_code( 403 );
	exit;
}

config_set_global( 'allow_anonymous_login', OFF );
require_api( 'user_api.php' );
require_api( 'project_api.php' );
require_api( 'access_api.php' );

$t_demo_account = 'tp-demo-anon';
$t_demo_project = 'DEMOSTRACION-TP-Correo-Anonimo';
$t_demo_user_id = user_get_id_by_name( $t_demo_account );
$t_demo_project_id = project_get_id_by_name( $t_demo_project, false );

if( false !== $t_demo_user_id
	&& null !== $t_demo_project_id
	&& user_get_field( $t_demo_user_id, 'enabled' )
	&& user_get_field( $t_demo_user_id, 'protected' )
	&& VIEWER === (int)user_get_field( $t_demo_user_id, 'access_level' )
	&& VS_PUBLIC === (int)project_get_field( $t_demo_project_id, 'view_state' )
	&& REPORTER === (int)access_get_project_level( $t_demo_project_id, $t_demo_user_id )
) {
	config_set_global( 'anonymous_account', $t_demo_account );
	config_set_global( 'allow_anonymous_login', ON );
}
