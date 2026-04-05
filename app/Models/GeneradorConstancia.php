<?php

namespace App\Models;

/**
 * @deprecated Use VisitCertificate instead.
 * This class is a shim to prevent ErrorException while refactoring.
 */
class GeneradorConstancia extends VisitCertificate
{
    protected $table = 'visit_certificates';

    // Map old field names in queries if needed, but better to refactor the queries.
}
