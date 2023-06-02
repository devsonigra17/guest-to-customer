<?php

namespace Dev\Guest\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;


class CreateCustomer implements ObserverInterface
{
    const MODULE_ENABLED = 'guestToCustomer/general/enable';
    protected $scopeConfig;
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )

    {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $order = $observer->getEvent()->getOrder();
        $websiteId = $this->storeManager->getWebsite()->getWebsiteId();
        $customer = $this->customerFactory->create();

        $enable = $this->scopeConfig->getValue(self::MODULE_ENABLED);
        if($enable)
        {
            if($order->getCustomerIsGuest()==1)
            {
                $email = $order->getCustomerEmail();
                $billingAddress = $order->getBillingAddress();
                $firstname = $billingAddress->getFirstname();
                $lastname = $billingAddress->getLastname();

                $customer->setWebsiteId($websiteId);
                $customer->setEmail($email);
                $customer->setFirstname($firstname);
                $customer->setLastname($lastname);
                $customer->setPassword('Dev@123');
                $customer->setTaxvat('856984526548562');
                $customer->setGender('1');
                $customer->setDob('2004-04-17');
                $customer->save();
                $success = __('Create User Successfully');
                $this->messageManager->addSuccess($success);
            }
        }
    }
}
