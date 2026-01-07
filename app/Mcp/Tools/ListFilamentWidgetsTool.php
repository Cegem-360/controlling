<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use Filament\Facades\Filament;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ListFilamentWidgetsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        List all Filament widgets registered in a panel. Returns widget class names,
        types, and configuration details.
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

        $widgets = $panel->getWidgets();

        $result = [];
        foreach ($widgets as $widgetClass) {
            $result[] = [
                'class' => $widgetClass,
                'name' => class_basename($widgetClass),
                'type' => $this->getWidgetType($widgetClass),
                'sort' => $this->getWidgetSort($widgetClass),
                'columns_span' => $this->getColumnSpan($widgetClass),
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
                ->description('The panel ID to list widgets from (default: admin)'),
        ];
    }

    private function getWidgetType(string $widgetClass): string
    {
        if (is_subclass_of($widgetClass, \Filament\Widgets\ChartWidget::class)) {
            return 'chart';
        }

        if (is_subclass_of($widgetClass, \Filament\Widgets\StatsOverviewWidget::class)) {
            return 'stats';
        }

        if (is_subclass_of($widgetClass, \Filament\Widgets\TableWidget::class)) {
            return 'table';
        }

        return 'custom';
    }

    private function getWidgetSort(string $widgetClass): ?int
    {
        if (property_exists($widgetClass, 'sort')) {
            return (new \ReflectionProperty($widgetClass, 'sort'))->getDefaultValue();
        }

        return null;
    }

    private function getColumnSpan(string $widgetClass): mixed
    {
        if (property_exists($widgetClass, 'columnSpan')) {
            return (new \ReflectionProperty($widgetClass, 'columnSpan'))->getDefaultValue();
        }

        return null;
    }
}
