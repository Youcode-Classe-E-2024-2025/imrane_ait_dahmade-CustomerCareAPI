<?php

namespace App\Services;

use App\Models\Categories;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Get all categories
     *
     * @return Collection
     */
    public function getAllCategories(): Collection
    {
        return Categories::orderBy('name')->get();
    }

    /**
     * Get a specific category by ID
     *
     * @param int $categoryId
     * @return Categories
     */
    public function getCategoryById(int $categoryId): Categories
    {
        return Categories::findOrFail($categoryId);
    }

    /**
     * Create a new category
     *
     * @param array $data
     * @return Categories
     */
    public function createCategory(array $data): Categories
    {
        return Categories::create($data);
    }

    /**
     * Update a category
     *
     * @param int $categoryId
     * @param array $data
     * @return Categories
     */
    public function updateCategory(int $categoryId, array $data): Categories
    {
        $categorie = Categories::findOrFail($categoryId);
        $categorie->update($data);
        return $categorie->fresh();
    }

    /**
     * Delete a category
     *
     * @param int $categoryId
     * @return bool
     */
    public function deleteCategory(int $categoryId): bool
    {
        $categorie = Categories::findOrFail($categoryId);
        return $categorie->delete();
    }
}