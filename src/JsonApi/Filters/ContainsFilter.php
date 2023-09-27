<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\JsonApi\Filters;

use LaravelJsonApi\Core\Support\Str;
use LaravelJsonApi\Eloquent\Contracts\Filter;
use LaravelJsonApi\Eloquent\Filters\Concerns\DeserializesValue;
use LaravelJsonApi\Eloquent\Filters\Concerns\IsSingular;

class ContainsFilter implements Filter
{
    use DeserializesValue;
    use IsSingular;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $column;

    /**
     * Create a new filter.
     *
     * @param string $name
     * @param string|null $column
     *
     * @return self
     */
    public static function make(string $name, string $column = null): self
    {
        return new static($name, $column);
    }

    /**
     * @param string $name
     * @param string|null $column
     */
    public function __construct(string $name, string $column = null)
    {
        $this->name = $name;
        $this->column = $column ?: Str::underscore($name);
    }

    /**
     * @ineheritDoc
     */
    public function apply($query, $value)
    {
        $query->where($query->qualifyColumn($this->column), 'like', '%' . $this->deserialize($value) . '%');
    }

    /**
     * @ineheritDoc
     */
    public function key(): string
    {
        return $this->name;
    }
}
