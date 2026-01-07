<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use App\Mcp\Tools\GetFilamentPanelInfoTool;
use App\Mcp\Tools\GetFilamentResourceSchemaTool;
use App\Mcp\Tools\ListFilamentPagesTool;
use App\Mcp\Tools\ListFilamentResourcesTool;
use App\Mcp\Tools\ListFilamentWidgetsTool;
use Laravel\Mcp\Server;

class FilamentServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Filament MCP Server';

    /**
     * The MCP server's version.
     */
    protected string $version = '1.0.0';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        This MCP server provides tools for introspecting and understanding Filament v4 admin panels.

        ## Available Tools

        - **list-filament-resources**: List all resources registered in a panel with their models, navigation info, and pages
        - **get-filament-resource-schema**: Get detailed form and table schema for a specific resource
        - **list-filament-widgets**: List all widgets in a panel with their types and configuration
        - **list-filament-pages**: List all custom pages in a panel with their routes and navigation
        - **get-filament-panel-info**: Get panel configuration including auth settings, plugins, and theme

        ## Usage

        Use these tools to understand the structure of Filament admin panels, discover available resources,
        and inspect form/table schemas before making modifications or generating code.

        All tools accept an optional `panel_id` parameter (defaults to 'admin').
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        ListFilamentResourcesTool::class,
        GetFilamentResourceSchemaTool::class,
        ListFilamentWidgetsTool::class,
        ListFilamentPagesTool::class,
        GetFilamentPanelInfoTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
