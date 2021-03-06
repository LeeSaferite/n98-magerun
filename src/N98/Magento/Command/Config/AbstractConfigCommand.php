<?php

namespace N98\Magento\Command\Config;

use N98\Magento\Command\AbstractMagentoCommand;

abstract class AbstractConfigCommand extends AbstractMagentoCommand
{
    /**
     * @return \Mage_Core_Model_Abstract
     */
    protected function getEncryptionModel()
    {
        return $this->_getModel('core/encryption', 'Mage_Core_Model_Encryption')
                    ->setHelper($this->getCoreHelper());
    }

    /**
     * @return \Mage_Core_Model_Abstract
     */
    protected function _getConfigDataModel()
    {
        return $this->_getModel('core/config_data', 'Mage_Core_Model_Config_Data');
    }

    /**
     * @param string $value
     * @param boolean $decryptionRequired
     * @return string
     */
    protected function _formatValue($value, $decryptionRequired)
    {
        if ($decryptionRequired) {
            $value = $this->getEncryptionModel()->decrypt($value);
        }

        return $value;
    }

    /**
     * @param string $scope
     */
    protected function _validateScopeParam($scope)
    {
        if (!in_array($scope, $this->_scopes)) {
            throw new \InvalidArgumentException(
                'Invalid scope parameter. It must be one of ' . implode(',', $this->_scopes)
            );
        }
    }

    /**
     * @param string $scope
     * @param string $scopeId
     *
     * @return string
     */
    protected function _convertScopeIdParam($scope, $scopeId)
    {
        if ($scope == 'websites' && !is_numeric($scopeId)) {
            $website = \Mage::app()->getWebsite($scopeId);
            if (!$website) {
                throw new \InvalidArgumentException('Invalid scope parameter. Website does not exist.');
            }

            return $website->getId();
        }

        if ($scope == 'stores' && !is_numeric($scopeId)) {
            $store = \Mage::app()->getStore($scopeId);
            if (!$store) {
                throw new \InvalidArgumentException('Invalid scope parameter. Store does not exist.');
            }

            return $store->getId();
        }

        return $scopeId;
    }

    /**
     * @return \Mage_Core_Model_Config
     */
    protected function _getConfigModel()
    {
        return $this->_getModel('core/config','Mage_Core_Model_Config');
    }
}