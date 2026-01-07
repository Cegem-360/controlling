<?php

declare(strict_types=1);

use App\Mcp\Servers\FilamentServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('filament', FilamentServer::class);
