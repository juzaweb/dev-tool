<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://juzaweb.com
 * @license    GNU V2
 */

namespace Juzaweb\DevTool\Support;

use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\Resource;
use Juzaweb\Backend\Models\Taxonomy;
use Juzaweb\CMS\Contracts\HookActionContract;

class DemoContentBuilder
{
    protected ?int $siteId = null;

    protected int $limit = 10;

    protected int $taxonomyLimit = 10;

    protected ?string $postType = null;

    public static function make()
    {
        return app()->make(self::class);
    }

    public function __construct(
        protected HookActionContract $hookAction
    ) {
    }

    public function config(): void
    {
        if ($this->getSiteId()) {
            $site = $this->siteManager->find($this->getSiteId());

            throw_if($site == null, new \Exception('Site not found'));

            $this->siteSetup->setup($site->model());
        }
    }

    public function resetAndGenerate(): void
    {
        $this->reset();
        $this->generate();
    }

    public function reset(): void
    {
        Taxonomy::query()->delete();
        Post::query()->delete();
    }

    public function generate(): void
    {
        $this->config();

        $this->generateTaxonomies();

        $postTypes = $this->hookAction->getPostTypes()
            ->where('key', '!=', 'pages')
            ->when($this->getPostType(), fn($query) => $query->where('key', $this->getPostType()));

        foreach ($postTypes as $postType) {
            $thumbnailSize = get_thumbnail_size($postType['key'], [$postType['key'] => [['width' => 640, 'height' => 480]]]);
            for ($i = 0; $i < $this->getLimit(); $i++) {
                $post = Post::factory()->create([
                    'type' => $postType['key'],
                    'thumbnail' => $this->randomThumbnailUrl(
                        $thumbnailSize['width'],
                        $thumbnailSize['height']
                    )
                ]);

                $resources = $this->hookAction->getResource()->where('post_type', $postType['key']);
                $randomLimit = random_int(5, 25);
                foreach ($resources as $resource) {
                    for ($j = 1; $j <= $randomLimit; $j++) {
                        Resource::factory()->create([
                            'type' => $resource['key'],
                            'post_id' => $post->id,
                        ]);
                    }
                }
            }
        }
    }

    public function setSiteId(?int $siteId): static
    {
        $this->siteId = $siteId;

        return $this;
    }

    public function getSiteId(): ?int
    {
        return $this->siteId;
    }

    public function setLimit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setPostType(?string $postType): static
    {
        $this->postType = $postType;

        return $this;
    }

    public function getPostType(): ?string
    {
        return $this->postType;
    }

    protected function generateTaxonomies(): void
    {
        $taxonomies = $this->hookAction->getTaxonomies()
            ->when($this->getPostType(), fn($query) => $query->where('post_type', $this->getPostType()));

        foreach ($taxonomies as $postType => $taxonomy) {
            foreach ($taxonomy as $item) {
                for ($i = 0; $i < $this->taxonomyLimit; $i++) {
                    Taxonomy::factory()->create(['taxonomy' => $item['taxonomy'], 'post_type' => $postType]);
                }
            }
        }
    }

    protected function randomThumbnailUrl(int $width = 640, int $height = 480): string
    {
        return app()->make(\Faker\Generator::class)->imageUrl(
            $width,
            $height,
            null,
            false,
            null,
            false,
            'jpg'
        );
    }
}
