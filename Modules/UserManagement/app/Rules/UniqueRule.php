<?php

namespace Modules\UserManagement\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueRule implements Rule
{
    protected $table;
    protected $column;
    protected $ignoreId;

    /**
     * Create a new rule instance.
     */
    public function __construct($table, $column, $ignoreId = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->ignoreId = $ignoreId;
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value)
    {
        $query = DB::table($this->table)
            ->where($this->column, $value);

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        return !$query->exists();
    }

    /**
     * Get the validation error message.
     */
    public function message()
    {
        return 'The :attribute is already in use.';
    }
}
