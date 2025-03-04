<?php

namespace App\Filament\Teacher\Pages;

use App\Models\Grade;
use App\Models\Student;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Pages\Page;
use Filament\Support\Enums\FontFamily;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class MyStudents extends Page implements HasTable
{
    use HasPageShield, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $activeNavigationIcon = 'heroicon-s-users';

    protected static string $view = 'filament.teacher.pages.my-students';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::query()
                    ->whereHas('grade.teachers', function ($query) {
                        $query->where('user_id', auth()->id());
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('student_id')
                    ->label('ID')
                    ->fontFamily(FontFamily::Mono),
                Tables\Columns\ImageColumn::make('user.image')
                    ->label('User Image')
                    ->circular()
                    ->defaultImageUrl(function (Student $record) {
                        $resolver = app(AvatarProvider::class);

                        return $resolver->get($record->user);
                    }),
                Tables\Columns\TextColumn::make('user.fullname')
                    ->label('Full Name'),
                Tables\Columns\TextColumn::make('grade.subjects.name')
                    ->label('Subjects')
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                SelectFilter::make('grade_id')
                    ->label('Grade')
                    ->relationship('grade', 'name')
                    ->placeholder('Filter by grade')
                    ->searchable()
                    ->multiple()
                    ->options(fn () => Grade::pluck('name', 'id')->toArray()),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->defaultGroup(
                Group::make('grade.name')
                    ->titlePrefixedWithLabel(false)
            );
    }
}
