<?php

declare(strict_types=1);

/*
 * This file is part of the Asdoria Package.
 *
 * (c) Asdoria .
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Asdoria\SyliusBulkEditPlugin\Action\Product;

use Asdoria\SyliusBulkEditPlugin\Action\ResourceActionInterface;
use Asdoria\SyliusBulkEditPlugin\Message\BulkEditNotificationInterface;
use Asdoria\SyliusBulkEditPlugin\Traits\TaxonRepositoryTrait;
use sdoria\SyliusBulkEditPlugin\Form\Type\Configuration\TaxonConfigurationType;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/**
 * Class RemoveProductTaxonAction
 */
final class RemoveProductTaxonAction implements ResourceActionInterface
{
    public const REMOVE_PRODUCT_TAXON = 'remove_product_taxon';

    use TaxonRepositoryTrait;

    public function handle(ResourceInterface $resource, BulkEditNotificationInterface $message): void
    {
        Assert::isInstanceOf($resource, ProductInterface::class);

        $configuration = $message->getConfiguration();

        if (empty($configuration)) {
            return;
        }

        $taxonCode = $configuration[TaxonConfigurationType::_TAXON_FIELD] ?? null;

        if (empty($taxonCode)) {
            return;
        }

        $taxon = $this->getTaxonRepository()->findOneByCode($taxonCode);

        Assert::isInstanceOf($taxon, TaxonInterface::class);

        $productTaxon = $resource->getProductTaxons()
            ->filter(fn ($current) => $current->getTaxon()->getId() === $taxon->getId())->first();

        if (empty($productTaxon)) {
            return;
        }

        $resource->removeProductTaxon($productTaxon);
    }
}
