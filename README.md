# Api Auth Bundle

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 ATENCIÓN!!!!!!!!!

Para completar este bundle falta por chequear las rutas para ver si
se cumplen los permisos especificados en las reglas de autorización.

Tal y como está ahora solo se usa la autenticación, es decir, solo
se comprueba si el usuario es quien dice ser y, en su caso, se permite
la ejecución de la operación
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ 


El propósito de este bundle es ofrecer una manera sencilla de proteger una API
mediante token, al estilo de OAuth2, pero sin tanto lío.

Una vez incorporado el bundle al proyecto, todas las peticiones que se realicen
serán interceptadas por un listener asociado a la clase ``TokenListener`` que
será el encargado de comprobar si la ruta de la petición está protegida, qué
roles se requieren para entrar en ella, y si el token que viene en la petición
está asociado a los roles requeridos por la ruta.

Por otra parte, al incorporar el plugin se añaden al proyecto las siguientes 
operaciones para gestionar los procesos de login, logout y recuperación del
password:

    POST /rpc/login
    {"username": "juanda", "password": "kakalaka"}

    POST /rpc/logout
    {"token": "llosiuwe8ejd7dk"}

    POST /rpc/get_forgot_password_token
    {"email": "juanda@yuido.com"}

    POST /rpc/set_password
    {"confirmation_token": "iisusuw7w7whshtsd6df", 
    "password": {"first": "kaka", "second": "kaka"} }

    POST /rpc/set_user_password
    {"token":"llosiuwe8ejd7dk", "oldPassword":"admin",
    "password":{"first":"kaka","second":"kaka"}}


## Instalación 

    yuido_api_auth:
        user_class: AppBundle\Entity\User
        group_class: AppBundle\Entity\Group
        access_control:
            - { path: ^/api, roles: ROLE_USER }
            - { path: ^/api/users, roles: ROLE_ADMIN }
            - { path: ^/rpc, roles: ROLE_USER }


## Protección de las operaciones de la API


Cada operación de una API de tipo RESTFul o REST RPC, está asociada a una ruta,
por lo tanto proteger una operación es equivalente a proteger a una ruta.

Este bundle protege las rutas a través de la configuración. En el ``config.yml``
añadir:

    yuido_api_auth:
        access_control:
            - { path: ^/api, roles: ROLE_USER }
            - { path: ^/api/users, roles: ROLE_ADMIN }
            - { path: ^/rpc, roles: ROLE_USER }

Hemos seguido la misma sintaxis de configuración que el componente de seguridad
de Symfony2 para los access control. A la hora de aplicar una regla también 
seguimos la misma lógica, se recorren todas las reglas en orden y la primera
que coincida con la ruta se aplica.

## Encoder

Se usa el componente de seguridad de Sf2. En ``security.yml``

    security:
        encoders:
            AppBundle\Entity\User: bcrypt

## Operaciones del bundle


### Login

Petición:

    POST /rpc/login
    {"username": "juanda", "password": "kakalaka"}

Respuesta:

    {"token":"578732cad981b",
     "user": {
        "id":21,
        "firstname":"Juanda",
        "lastname":"Rodríguez",
        "username":"juanda",
        "email":"juanda@yuido.com",
        "role":"admin"
        }
    }

En caso de error en el login devuelve un statusCode = 401 (Unauthorized)

Esta acción crea un token asociado al usuario y lo devuelve en la respuesta.

### Logout

Petición:
    POST /rpc/logout
    {"token": "llosiuwe8ejd7dk"}

Esta acción elimina el token asociado al usuario y devuelve una respuesta vacía.

### Obtención de un token de confirmación para cambiar el password


Esta acción recoge una dirección de email y, si está registrado en el sistema,
genera un token de confirmación y envía un link con dicho token a la dirección
de email dada. Con ese link el usuario puede cambiar el password.

Petición:

    POST /rpc/get_forgot_password_token
    {"email": "juanda@yuido.com"}

Respuesta:

    code 200
    {"message":"Se le ha enviado un enlace para restablecer su direcci\u00f3n de correo"}

    code 500
    {"errors":{"email":["mensaje error 1","mensaje error 2"]}}

### Definición del password de un usuario

Esta acción define el password de un usuario que ha solicitado su recuperación.
Para ello requiere enviar en la petición el token de confirmación.

Peticion:

    POST /set_password/token=iisusuw7w7whshtsd6df
    {"password": {"first": "kaka", "second": "kaka"} }

Respuesta:

    code 200:
    {"message":"password actualizado"}

    code 500:
    {"errors":["El token enviado no es v\u00e1lido"]}

### Cambio de password de un usuario

Esta operación permite cambiar el password al usuario asociado al token
que se envía.

Petición:

    POST /set_user_password
    {"token":"llosiuwe8ejd7dk", "oldPassword":"admin",
    "password":{"first":"kaka","second":"kaka"}}

Respuesta:

    code: 200
    {"message":"password actualizado"}

    code: 500
    {"errors":["mensaje de error 1", "mensaje de error 2"]}


## Entidades User y Group


El bundle incorpora al proyecto las entidades ``User`` y ``Group``,la cuales son
abstractas y están basadas en el bundle fos_user. Estas entidades deben ser
extendidas en el proyecto y definidas en la configuración del bundle:

yuido_api_auth:
    user_class: AppBundle\Entity\User
    group_class: AppBundle\Entity\Group

Un ejemplo de extensión de la clase ``User``

    // AppBundle\Entity\User
    <?php
    namespace AppBundle\Entity;

    use Yuido\ApiAuthBundle\Entity\User as BaseUser;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="yuido_users")
     */
    class User extends BaseUser
    {    
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        protected $nuevoAtributo;

        public function getNuevoAtributo(){
            return $this->nuevoAtributo();
        }

        public setNuevoAtributo($a){
            $this->nuevoAtributo = $a;
        }

    }

Y un ejemplo de extensión de la clase Group

    // AppBundle\Entity\Group
    <?php
    namespace AppBundle\Entity;

    use Yuido\ApiAuthBundle\Entity\Group as BaseGroup;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="yuido_groups")
     */
    class Group extends BaseGroup
    {    
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        protected $nuevoAtributo;

        public function getNuevoAtributo(){
            return $this->nuevoAtributo();
        }

        public setNuevoAtributo($a){
            $this->nuevoAtributo = $a;
        }

    }
