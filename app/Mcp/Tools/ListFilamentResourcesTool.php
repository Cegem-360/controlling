<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use Filament\Facades\Filament;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ListFilamentResourcesTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        List all Filament resources registered in a panel. Returns resource class names,
        model associations, navigation labels, and available pages for each resource.
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

        $resources = $panel->getResources();

        $result = [];
        foreach ($resources as $resourceClass) {
            /** @var class-string<\Filament\Resources\Resource> $resourceClass */
            $result[] = [
                'class' => $resourceClass,
                'model' => $resourceClass::getModel(),
                'navigation_label' => $resourceClass::getNavigationLabel(),
                'navigation_group' => $resourceClass::getNavigationGroup(),
                'navigation_icon' => $this->getIconName($resourceClass::getNavigationIcon()),
                'slug' => $resourceClass::getSlug(),
                'pages' => array_keys($resourceClass::getPages()),
                'relations' => array_map(class_basename(...), $resourceClass::getRelations()),
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
                ->description('The panel ID to list resources from (default: admin)'),
        ];
    }

    private function getIconName(mixed $icon): ?string
    {
        if ($icon === null) {
            return null;
        }

        if (is_string($icon)) {
            return $icon;
        }

        if ($icon instanceof \BackedEnum) {
            return $icon->value;
        }

        return null;
    }
}
