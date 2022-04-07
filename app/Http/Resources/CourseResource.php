<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"=> $this->id,
            "category_id"=> $this->category_id,
            "teacher_id"=> $this->teacher_id,
            "total_students" => $this->when(isset($this->total_students), $this->total_students),
            "students_on_progress" => $this->when(isset($this->students_on_progress), $this->students_on_progress),
            "students_completed" => $this->when(isset($this->students_completed), $this->students_completed),
            "title"=> $this->title,
            "price"=> $this->price,
            "desc"=> $this->desc,
            "level"=> $this->level,
            "thumbnail"=> $this->thumbnail,
            "hero_background"=> $this->hero_background,
            "estimated_time"=> $this->estimated_time,
            "trailer"=> $this->trailer,
            "language"=> $this->language,
            "status"=> $this->status,
            "is_on_discount"=> $this->is_on_discount,
            "discount_price"=> $this->discount_price,
            "slug"=> $this->slug,
            "created_at"=> $this->created_at,
            "updated_at"=> $this->updated_at,
            "teacher" => $this->whenLoaded('teacher'),
            "category" => $this->whenLoaded('category'),
            "course_sections" => $this->whenLoaded('course_sections'),
        ];
    }
}
