<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use Filament\Facades\Filament;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ListFilamentPagesTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        List all Filament pages registered in a panel. Returns page class names,
        routes, navigation labels, and icons.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $panelId = $request->get('panel_id', 'admin');

        try {
            $panel = Filament::getPanel($panelId);

            if ($panel === null) {
                return Response::error("Panel '{$panelId}' not found.");
            }

            Filament::setCurrentPanel($panel);
        } catch (\Exception $e) {
            return Response::error("Panel '{$panelId}' not found.");
        }

        $pages = $panel->getPages();

        $result = [];
        foreach ($pages as $pageClass) {
            $result[] = [
                'class' => $pageClass,
                'name' => class_basename($pageClass),
                'slug' => $pageClass::getSlug(),
                'navigation_label' => $pageClass::getNavigationLabel(),
                'navigation_group' => $pageClass::getNavigationGroup(),
                'navigation_icon' => $this->getIconName($pageClass::getNavigationIcon()),
                'route_name' => $pageClass::getRouteName(),
            ];
        }

        return Response::text(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'panel_id' => $schema->string()
                ->description('The panel ID to list pages from (default: admin)'),
        ];
    }

    private function getIconName(mixed $icon): ?string
    {
        if ($icon === null) {
            return null;
        }

        if (\is_string($icon)) {
            return $icon;
        }

        if ($icon instanceof \BackedEnum) {
            return $icon->value;
        }

        return null;
    }
}
