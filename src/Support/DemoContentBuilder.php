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
use Juzaweb\Backend\Models\Taxonomy;
use Juzaweb\CMS\Contracts\HookActionContract;
use Juzaweb\Network\Contracts\SiteManagerContract;
use Juzaweb\Network\Contracts\SiteSetupContract;

class DemoContentBuilder
{
    protected ?int $siteId = null;

    protected int $limit = 10;

    protected int $taxonomyLimit = 10;

    public static function make()
    {
        return app()->make(self::class);
    }

    public function __construct(
        protected HookActionContract $hookAction,
        protected SiteSetupContract $siteSetup,
        protected SiteManagerContract $siteManager
    ) {
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

        $postTypes = $this->hookAction->getPostTypes()->where('key', '!=', 'pages');

        foreach ($postTypes as $postType) {
            $thumbnailSize = get_thumbnail_size($postType['key'], [$postType['key'] => [['width' => 640, 'height' => 480]]]);
            for ($i = 0; $i < $this->limit; $i++) {
                Post::factory()->create([
                    'type' => $postType['key'],
                    'thumbnail' => $this->randomThumbnailUrl(
                        $thumbnailSize['width'],
                        $thumbnailSize['height']
                    )
                ]);

                $resources = $this->hookAction->getResource();
                dd($resources);
            }
        }
    }

    protected function generateTaxonomies(): void
    {
        $taxonomies = $this->hookAction->getTaxonomies();

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
