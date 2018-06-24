<?php
declare(strict_types=1);

namespace Yireo\ExampleViewModelWithConfiguration\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Product
 *
 * @package Yireo\ExampleViewModelWithConfiguration\ViewModel
 */
class Product implements ArgumentInterface
{
    const DEFAULT_PAGESIZE = 3;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Product constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        ScopeConfigInterface $scopeConfig
    ) {

        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return ProductInterface[]
     */
    public function getProducts(): array
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $attributeName = $this->getAttributeNameToFilter();
        $attributeValue = $this->getAttributeValueToFilter();
        if ($attributeName && $attributeValue) {
            $searchCriteriaBuilder->addFilter($attributeName, $attributeValue, 'like');
        }

        $searchCriteriaBuilder->setPageSize($this->getPageSize());
        $searchCriteria = $searchCriteriaBuilder->create();

        $searchResult = $this->productRepository->getList($searchCriteria);
        $products = $searchResult->getItems();

        return $products;
    }

    /**
     * @return string
     */
    private function getAttributeNameToFilter(): string
    {
        return (string) $this->scopeConfig->getValue('example_view_model/settings/attribute_name');
    }

    /**
     * @return string
     */
    private function getAttributeValueToFilter(): string
    {
        return (string) $this->scopeConfig->getValue('example_view_model/settings/attribute_value');
    }

    /**
     * @return int
     */
    private function getPageSize(): int
    {
        $pageSize = (int) $this->scopeConfig->getValue('example_view_model/settings/product_count');

        if ($pageSize) {
            return $pageSize;
        }

        return self::DEFAULT_PAGESIZE;
    }
}