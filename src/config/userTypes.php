<?php
// config/userTypes.php – Central definition of all user role identifiers.

class UserTypes {
    const LOCADOR   = 'locador';
    const LOCATARIO = 'locatario';
    const GERENTE   = 'gerente';

    const ALL = [self::LOCADOR, self::LOCATARIO, self::GERENTE];
}
