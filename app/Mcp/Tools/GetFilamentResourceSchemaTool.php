<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use Filament\Facades\Filament;
use Filament\Tables\Columns\Column;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetFilamentResourceSchemaTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get the form and table schema for a specific Filament resource.
        Returns form fields, table columns, filters, and actions.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $resourceClass = $request->get('resource');
        $panelId = $request->get('panel_id', 'admin');

        if (! class_exists($resourceClass)) {
            return Response::error("Resource class '{$resourceClass}' not found.");
        }

        try {
            $panel = Filament::getPanel($panelId);

            if ($panel === null) {
                return Response::error("Panel '{$panelId}' not found.");
            }

            Filament::setCurrentPanel($panel);
        } catch (\Exception $e) {
            return Response::error("Panel '{$panelId}' not found.");
        }

        $result = [
            'resource' => $resourceClass,
            'model' => $resourceClass::getModel(),
            'form' => $this->getFormSchema($resourceClass),
            'table' => $this->getTableSchema($resourceClass),
        ];

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
            'resource' => $schema->string()
                ->description('The fully qualified resource class name')
                ->required(),
            'panel_id' => $schema->string()
                ->description('The panel ID (default: admin)'),
        ];
    }

    /**
     * @param  class-string  $resourceClass
     */
    private function getFormSchema(string $resourceClass): array
    {
        try {
            $model = $resourceClass::getModel();
            $record = new $model();

            $schema = $resourceClass::form(
                \Filament\Schemas\Schema::make(new \Filament\Resources\Pages\CreateRecord()),
            );

            return $this->extractSchemaComponents($schema->getComponents());
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * @param  class-string  $resourceClass
     */
    private function getTableSchema(string $resourceClass): array
    {
        try {
            $table = $resourceClass::table(
                \Filament\Tables\Table::make(new \Filament\Resources\Pages\ListRecords()),
            );

            $columns = collect($table->getColumns())
                ->map(fn (Column $column) => [
                    'name' => $column->getName(),
                    'label' => $column->getLabel(),
                    'type' => class_basename($column),
                    'sortable' => $column->isSortable(),
                    'searchable' => $column->isSearchable(),
                ])
                ->values()
                ->all();

            $filters = collect($table->getFilters())
                ->map(fn ($filter) => [
                    'name' => $filter->getName(),
                    'label' => $filter->getLabel(),
                    'type' => class_basename($filter),
                ])
                ->values()
                ->all();

            return [
                'columns' => $columns,
                'filters' => $filters,
            ];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function extractSchemaComponents(array $components): array
    {
        return collect($components)
            ->map(function ($component) {
                $data = [
                    'type' => class_basename($component),
                ];

                if (method_exists($component, 'getName')) {
                    $data['name'] = $component->getName();
                }

                if (method_exists($component, 'getLabel')) {
                    $data['label'] = $component->getLabel();
                }

                if (method_exists($component, 'isRequired')) {
                    $data['required'] = $component->isRequired();
                }

                if (method_exists($component, 'getChildComponents')) {
                    $children = $component->getChildComponents();
                    if (! empty($children)) {
                        $data['children'] = $this->extractSchemaComponents($children);
                    }
                }

                return $data;
            })
            ->values()
            ->all();
    }
}
