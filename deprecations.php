<?php


$deprecatedIdentifiers = [
    'JRegistry'           => 'use Joomla\Registry\Registry',
    'JRegistryFormatIni'  => 'use Joomla\Registry\Format\Ini',
    'JRegistryFormatJson' => 'use Joomla\Registry\Format\Json',
    'JRegistryFormatPhp'  => 'use Joomla\Registry\Format\Php',
    'JRegistryFormatXml'  => 'use Joomla\Registry\Format\Xml',
    'JStringInflector'    => 'use Joomla\String\Inflector',
    'JStringNormalise'    => 'use Joomla\String\Normalise',
    'JData'               => 'use Joomla\Data\DataObject',
    'JDataSet'            => 'use Joomla\Data\DataSet',
    'JDataDumpable'       => 'use Joomla\Data\DumpableInterface',

    'JApplicationAdministrator' => 'use Joomla\CMS\Application\AdministratorApplication',
    'JApplicationHelper'        => 'use Joomla\CMS\Application\ApplicationHelper',
    'JApplicationBase'          => 'use Joomla\CMS\Application\BaseApplication',
    'JApplicationCli'           => 'use Joomla\CMS\Application\CliApplication',
    'JApplicationCms'           => 'use Joomla\CMS\Application\CMSApplication',
    'JApplicationDaemon'        => 'use Joomla\CMS\Application\DaemonApplication',
    'JApplicationSite'          => 'use Joomla\CMS\Application\SiteApplication',
    'JApplicationWeb'           => 'use Joomla\CMS\Application\WebApplication',
    'JApplicationWebClient'     => 'use Joomla\Application\Web\WebClient',
    'JDaemon'                   => 'use Joomla\CMS\Application\DaemonApplication',
    'JCli'                      => 'use Joomla\CMS\Application\CliApplication',
    'JWeb'                      => 'use Joomla\CMS\Application\WebApplication',
    'JWebClient'                => 'use Joomla\Application\Web\WebClient',

    'JModelAdmin'          => 'use Joomla\CMS\MVC\Model\AdminModel',
    'JModelForm'           => 'use Joomla\CMS\MVC\Model\FormModel',
    'JModelItem'           => 'use Joomla\CMS\MVC\Model\ItemModel',
    'JModelList'           => 'use Joomla\CMS\MVC\Model\ListModel',
    'JModelLegacy'         => 'use Joomla\CMS\MVC\Model\BaseDatabaseModel',
    'JViewCategories'      => 'use Joomla\CMS\MVC\View\CategoriesView',
    'JViewCategory'        => 'use Joomla\CMS\MVC\View\CategoryView',
    'JViewCategoryfeed'    => 'use Joomla\CMS\MVC\View\CategoryFeedView',
    'JViewLegacy'          => 'use Joomla\CMS\MVC\View\HtmlView',
    'JControllerAdmin'     => 'use Joomla\CMS\MVC\Controller\AdminController',
    'JControllerLegacy'    => 'use Joomla\CMS\MVC\Controller\BaseController',
    'JControllerForm'      => 'use Joomla\CMS\MVC\Controller\FormController',
    'JTableInterface'      => 'use Joomla\CMS\Table\TableInterface',
    'JTable'               => 'use Joomla\CMS\Table\Table',
    'JTableNested'         => 'use Joomla\CMS\Table\Nested',
    'JTableAsset'          => 'use Joomla\CMS\Table\Asset',
    'JTableExtension'      => 'use Joomla\CMS\Table\Extension',
    'JTableLanguage'       => 'use Joomla\CMS\Table\Language',
    'JTableUpdate'         => 'use Joomla\CMS\Table\Update',
    'JTableUpdatesite'     => 'use Joomla\CMS\Table\UpdateSite',
    'JTableUser'           => 'use Joomla\CMS\Table\User',
    'JTableUsergroup'      => 'use Joomla\CMS\Table\Usergroup',
    'JTableViewlevel'      => 'use Joomla\CMS\Table\ViewLevel',
    'JTableContenthistory' => 'use Joomla\CMS\Table\ContentHistory',
    'JTableContenttype'    => 'use Joomla\CMS\Table\ContentType',
    'JTableCorecontent'    => 'use Joomla\CMS\Table\CoreContent',
    'JTableUcm'            => 'use Joomla\CMS\Table\Ucm',
    'JTableCategory'       => 'use Joomla\CMS\Table\Category',
    'JTableContent'        => 'use Joomla\CMS\Table\Content',
    'JTableMenu'           => 'use Joomla\CMS\Table\Menu',
    'JTableMenuType'       => 'use Joomla\CMS\Table\MenuType',
    'JTableModule'         => 'use Joomla\CMS\Table\Module',

    'JAccess'                    => 'use Joomla\CMS\Access\Access',
    'JAccessRule'                => 'use Joomla\CMS\Access\Rule',
    'JAccessRules'               => 'use Joomla\CMS\Access\Rules',
    'JAccessExceptionNotallowed' => 'use Joomla\CMS\Access\Exception\NotAllowed',
    'JRule'                      => 'use Joomla\CMS\Access\Rule',
    'JRules'                     => 'use Joomla\CMS\Access\Rules',

    'JHelp'    => 'use Joomla\CMS\Help\Help',
    'JCaptcha' => 'use Joomla\CMS\Captcha\Captcha',

    'JLanguageAssociations'  => 'use Joomla\CMS\Language\Associations',
    'JLanguage'              => 'use Joomla\CMS\Language\Language',
    'JLanguageHelper'        => 'use Joomla\CMS\Language\LanguageHelper',
    'JLanguageMultilang'     => 'use Joomla\CMS\Language\Multilanguage',
    'JText'                  => 'use Joomla\CMS\Language\Text',
    'JLanguageTransliterate' => 'use Joomla\CMS\Language\Transliterate',

    'JComponentHelper'                  => 'use Joomla\CMS\Component\ComponentHelper',
    'JComponentRecord'                  => 'use Joomla\CMS\Component\ComponentRecord',
    'JComponentExceptionMissing'        => 'use Joomla\CMS\Component\Exception\MissingComponentException',
    'JComponentRouterBase'              => 'use Joomla\CMS\Component\Router\RouterBase',
    'JComponentRouterInterface'         => 'use Joomla\CMS\Component\Router\RouterInterface',
    'JComponentRouterLegacy'            => 'use Joomla\CMS\Component\Router\RouterLegacy',
    'JComponentRouterView'              => 'use Joomla\CMS\Component\Router\RouterView',
    'JComponentRouterViewconfiguration' => 'use Joomla\CMS\Component\Router\RouterViewConfiguration',
    'JComponentRouterRulesMenu'         => 'use Joomla\CMS\Component\Router\Rules\MenuRules',
    'JComponentRouterRulesNomenu'       => 'use Joomla\CMS\Component\Router\Rules\NomenuRules',
    'JComponentRouterRulesInterface'    => 'use Joomla\CMS\Component\Router\Rules\RulesInterface',
    'JComponentRouterRulesStandard'     => 'use Joomla\CMS\Component\Router\Rules\StandardRules',

    'JEditor' => 'use Joomla\CMS\Editor\Editor',

    'JErrorPage' => 'use Joomla\CMS\Exception\ExceptionHandler',

    'JAuthenticationHelper' => 'use Joomla\CMS\Helper\AuthenticationHelper',
    'JHelper'               => 'use Joomla\CMS\Helper\CMSHelper',
    'JHelperContent'        => 'use Joomla\CMS\Helper\ContentHelper',
    'JLibraryHelper'        => 'use Joomla\CMS\Helper\LibraryHelper',
    'JHelperMedia'          => 'use Joomla\CMS\Helper\MediaHelper',
    'JModuleHelper'         => 'use Joomla\CMS\Helper\ModuleHelper',
    'JHelperRoute'          => 'use Joomla\CMS\Helper\RouteHelper',
    'JHelperTags'           => 'use Joomla\CMS\Helper\TagsHelper',
    'JHelperUsergroups'     => 'use Joomla\CMS\Helper\UserGroupsHelper',

    'JLayoutBase'   => 'use Joomla\CMS\Layout\BaseLayout',
    'JLayoutFile'   => 'use Joomla\CMS\Layout\FileLayout',
    'JLayoutHelper' => 'use Joomla\CMS\Layout\LayoutHelper',
    'JLayout'       => 'use Joomla\CMS\Layout\LayoutInterface',

    'JResponseJson' => 'use Joomla\CMS\Response\JsonResponse',

    'JPlugin'       => 'use Joomla\CMS\Plugin\CMSPlugin',
    'JPluginHelper' => 'use Joomla\CMS\Plugin\PluginHelper',

    'JMenu'              => 'use Joomla\CMS\Menu\AbstractMenu',
    'JMenuAdministrator' => 'use Joomla\CMS\Menu\AdministratorMenu',
    'JMenuItem'          => 'use Joomla\CMS\Menu\MenuItem',
    'JMenuSite'          => 'use Joomla\CMS\Menu\SiteMenu',

    'JPagination'       => 'use Joomla\CMS\Pagination\Pagination',
    'JPaginationObject' => 'use Joomla\CMS\Pagination\PaginationObject',

    'JPathway'     => 'use Joomla\CMS\Pathway\Pathway',
    'JPathwaySite' => 'use Joomla\CMS\Pathway\SitePathway',

    'JSchemaChangeitem'           => 'use Joomla\CMS\Schema\ChangeItem',
    'JSchemaChangeset'            => 'use Joomla\CMS\Schema\ChangeSet',
    'JSchemaChangeitemMysql'      => 'use Joomla\CMS\Schema\ChangeItem\MysqlChangeItem',
    'JSchemaChangeitemPostgresql' => 'use Joomla\CMS\Schema\ChangeItem\PostgresqlChangeItem',

    'JUcm'        => 'use Joomla\CMS\UCM\UCM',
    'JUcmBase'    => 'use Joomla\CMS\UCM\UCMBase',
    'JUcmContent' => 'use Joomla\CMS\UCM\UCMContent',
    'JUcmType'    => 'use Joomla\CMS\UCM\UCMType',

    'JToolbar'                => 'use Joomla\CMS\Toolbar\Toolbar',
    'JToolbarButton'          => 'use Joomla\CMS\Toolbar\ToolbarButton',
    'JToolbarButtonConfirm'   => 'use Joomla\CMS\Toolbar\Button\ConfirmButton',
    'JToolbarButtonCustom'    => 'use Joomla\CMS\Toolbar\Button\CustomButton',
    'JToolbarButtonHelp'      => 'use Joomla\CMS\Toolbar\Button\HelpButton',
    'JToolbarButtonLink'      => 'use Joomla\CMS\Toolbar\Button\LinkButton',
    'JToolbarButtonPopup'     => 'use Joomla\CMS\Toolbar\Button\PopupButton',
    'JToolbarButtonSeparator' => 'use Joomla\CMS\Toolbar\Button\SeparatorButton',
    'JToolbarButtonStandard'  => 'use Joomla\CMS\Toolbar\Button\StandardButton',
    'JToolbarHelper'          => 'use Joomla\CMS\Toolbar\ToolbarHelper',
    'JButton'                 => 'use Joomla\CMS\Toolbar\ToolbarButton',

    'JVersion' => 'use Joomla\CMS\Version',

    'JAuthentication'         => 'use Joomla\CMS\Authentication\Authentication',
    'JAuthenticationResponse' => 'use Joomla\CMS\Authentication\AuthenticationResponse',

    'JBrowser' => 'use Joomla\CMS\Environment\Browser',

    'JAssociationExtensionInterface' => 'use Joomla\CMS\Association\AssociationExtensionInterface',
    'JAssociationExtensionHelper'    => 'use Joomla\CMS\Association\AssociationExtensionHelper',

    'JDocument'                      => 'use Joomla\CMS\Document\Document',
    'JDocumentError'                 => 'use Joomla\CMS\Document\ErrorDocument',
    'JDocumentFeed'                  => 'use Joomla\CMS\Document\FeedDocument',
    'JDocumentHtml'                  => 'use Joomla\CMS\Document\HtmlDocument',
    'JDocumentImage'                 => 'use Joomla\CMS\Document\ImageDocument',
    'JDocumentJson'                  => 'use Joomla\CMS\Document\JsonDocument',
    'JDocumentOpensearch'            => 'use Joomla\CMS\Document\OpensearchDocument',
    'JDocumentRaw'                   => 'use Joomla\CMS\Document\RawDocument',
    'JDocumentRenderer'              => 'use Joomla\CMS\Document\DocumentRenderer',
    'JDocumentXml'                   => 'use Joomla\CMS\Document\XmlDocument',
    'JDocumentRendererFeedAtom'      => 'use Joomla\CMS\Document\Renderer\Feed\AtomRenderer',
    'JDocumentRendererFeedRss'       => 'use Joomla\CMS\Document\Renderer\Feed\RssRenderer',
    'JDocumentRendererHtmlComponent' => 'use Joomla\CMS\Document\Renderer\Html\ComponentRenderer',
    'JDocumentRendererHtmlHead'      => 'use Joomla\CMS\Document\Renderer\Html\HeadRenderer',
    'JDocumentRendererHtmlMessage'   => 'use Joomla\CMS\Document\Renderer\Html\MessageRenderer',
    'JDocumentRendererHtmlModule'    => 'use Joomla\CMS\Document\Renderer\Html\ModuleRenderer',
    'JDocumentRendererHtmlModules'   => 'use Joomla\CMS\Document\Renderer\Html\ModulesRenderer',
    'JDocumentRendererAtom'          => 'use Joomla\CMS\Document\Renderer\Feed\AtomRenderer',
    'JDocumentRendererRSS'           => 'use Joomla\CMS\Document\Renderer\Feed\RssRenderer',
    'JDocumentRendererComponent'     => 'use Joomla\CMS\Document\Renderer\Html\ComponentRenderer',
    'JDocumentRendererHead'          => 'use Joomla\CMS\Document\Renderer\Html\HeadRenderer',
    'JDocumentRendererMessage'       => 'use Joomla\CMS\Document\Renderer\Html\MessageRenderer',
    'JDocumentRendererModule'        => 'use Joomla\CMS\Document\Renderer\Html\ModuleRenderer',
    'JDocumentRendererModules'       => 'use Joomla\CMS\Document\Renderer\Html\ModulesRenderer',
    'JFeedEnclosure'                 => 'use Joomla\CMS\Document\Feed\FeedEnclosure',
    'JFeedImage'                     => 'use Joomla\CMS\Document\Feed\FeedImage',
    'JFeedItem'                      => 'use Joomla\CMS\Document\Feed\FeedItem',
    'JOpenSearchImage'               => 'use Joomla\CMS\Document\Opensearch\OpensearchImage',
    'JOpenSearchUrl'                 => 'use Joomla\CMS\Document\Opensearch\OpensearchUrl',

    'JFilterInput'  => 'use Joomla\CMS\Filter\InputFilter',
    'JFilterOutput' => 'use Joomla\CMS\Filter\OutputFilter',

    'JHttp'                => 'use Joomla\CMS\Http\Http',
    'JHttpFactory'         => 'use Joomla\CMS\Http\HttpFactory',
    'JHttpResponse'        => 'use Joomla\CMS\Http\Response',
    'JHttpTransport'       => 'use Joomla\CMS\Http\TransportInterface',
    'JHttpTransportCurl'   => 'use Joomla\CMS\Http\Transport\CurlTransport',
    'JHttpTransportSocket' => 'use Joomla\CMS\Http\Transport\SocketTransport',
    'JHttpTransportStream' => 'use Joomla\CMS\Http\Transport\StreamTransport',

    'JInstaller'                 => 'use Joomla\CMS\Installer\Installer',
    'JInstallerAdapter'          => 'use Joomla\CMS\Installer\InstallerAdapter',
    'JInstallerExtension'        => 'use Joomla\CMS\Installer\InstallerExtension',
    'JExtension'                 => 'use Joomla\CMS\Installer\InstallerExtension',
    'JInstallerHelper'           => 'use Joomla\CMS\Installer\InstallerHelper',
    'JInstallerScript'           => 'use Joomla\CMS\Installer\InstallerScript',
    'JInstallerManifest'         => 'use Joomla\CMS\Installer\Manifest',
    'JInstallerAdapterComponent' => 'use Joomla\CMS\Installer\Adapter\ComponentAdapter',
    'JInstallerComponent'        => 'use Joomla\CMS\Installer\Adapter\ComponentAdapter',
    'JInstallerAdapterFile'      => 'use Joomla\CMS\Installer\Adapter\FileAdapter',
    'JInstallerFile'             => 'use Joomla\CMS\Installer\Adapter\FileAdapter',
    'JInstallerAdapterLanguage'  => 'use Joomla\CMS\Installer\Adapter\LanguageAdapter',
    'JInstallerLanguage'         => 'use Joomla\CMS\Installer\Adapter\LanguageAdapter',
    'JInstallerAdapterLibrary'   => 'use Joomla\CMS\Installer\Adapter\LibraryAdapter',
    'JInstallerLibrary'          => 'use Joomla\CMS\Installer\Adapter\LibraryAdapter',
    'JInstallerAdapterModule'    => 'use Joomla\CMS\Installer\Adapter\ModuleAdapter',
    'JInstallerModule'           => 'use Joomla\CMS\Installer\Adapter\ModuleAdapter',
    'JInstallerAdapterPackage'   => 'use Joomla\CMS\Installer\Adapter\PackageAdapter',
    'JInstallerPackage'          => 'use Joomla\CMS\Installer\Adapter\PackageAdapter',
    'JInstallerAdapterPlugin'    => 'use Joomla\CMS\Installer\Adapter\PluginAdapter',
    'JInstallerPlugin'           => 'use Joomla\CMS\Installer\Adapter\PluginAdapter',
    'JInstallerAdapterTemplate'  => 'use Joomla\CMS\Installer\Adapter\TemplateAdapter',
    'JInstallerTemplate'         => 'use Joomla\CMS\Installer\Adapter\TemplateAdapter',
    'JInstallerManifestLibrary'  => 'use Joomla\CMS\Installer\Manifest\LibraryManifest',
    'JInstallerManifestPackage'  => 'use Joomla\CMS\Installer\Manifest\PackageManifest',

    'JRouterAdministrator' => 'use Joomla\CMS\Router\AdministratorRouter',
    'JRoute'               => 'use Joomla\CMS\Router\Route',
    'JRouter'              => 'use Joomla\CMS\Router\Router',
    'JRouterSite'          => 'use Joomla\CMS\Router\SiteRouter',

    'JCategories'   => 'use Joomla\CMS\Categories\Categories',
    'JCategoryNode' => 'use Joomla\CMS\Categories\CategoryNode',

    'JDate' => 'use Joomla\CMS\Date\Date',

    'JLog'                    => 'use Joomla\CMS\Log\Log',
    'JLogEntry'               => 'use Joomla\CMS\Log\LogEntry',
    'JLogLogger'              => 'use Joomla\CMS\Log\Logger',
    'JLogger'                 => 'use Joomla\CMS\Log\Logger',
    'JLogLoggerCallback'      => 'use Joomla\CMS\Log\Logger\CallbackLogger',
    'JLogLoggerDatabase'      => 'use Joomla\CMS\Log\Logger\DatabaseLogger',
    'JLogLoggerEcho'          => 'use Joomla\CMS\Log\Logger\EchoLogger',
    'JLogLoggerFormattedtext' => 'use Joomla\CMS\Log\Logger\FormattedtextLogger',
    'JLogLoggerMessagequeue'  => 'use Joomla\CMS\Log\Logger\MessagequeueLogger',
    'JLogLoggerSyslog'        => 'use Joomla\CMS\Log\Logger\SyslogLogger',
    'JLogLoggerW3c'           => 'use Joomla\CMS\Log\Logger\W3cLogger',

    'JProfiler' => 'use Joomla\CMS\Profiler\Profiler',

    'JUri' => 'use Joomla\CMS\Uri\Uri',

    'JCache'                     => 'use Joomla\CMS\Cache\Cache',
    'JCacheController'           => 'use Joomla\CMS\Cache\CacheController',
    'JCacheStorage'              => 'use Joomla\CMS\Cache\CacheStorage',
    'JCacheControllerCallback'   => 'use Joomla\CMS\Cache\Controller\CallbackController',
    'JCacheControllerOutput'     => 'use Joomla\CMS\Cache\Controller\OutputController',
    'JCacheControllerPage'       => 'use Joomla\CMS\Cache\Controller\PageController',
    'JCacheControllerView'       => 'use Joomla\CMS\Cache\Controller\ViewController',
    'JCacheStorageApcu'          => 'use Joomla\CMS\Cache\Storage\ApcuStorage',
    'JCacheStorageHelper'        => 'use Joomla\CMS\Cache\Storage\CacheStorageHelper',
    'JCacheStorageFile'          => 'use Joomla\CMS\Cache\Storage\FileStorage',
    'JCacheStorageMemcached'     => 'use Joomla\CMS\Cache\Storage\MemcachedStorage',
    'JCacheStorageRedis'         => 'use Joomla\CMS\Cache\Storage\RedisStorage',
    'JCacheException'            => 'use Joomla\CMS\Cache\Exception\CacheExceptionInterface',
    'JCacheExceptionConnecting'  => 'use Joomla\CMS\Cache\Exception\CacheConnectingException',
    'JCacheExceptionUnsupported' => 'use Joomla\CMS\Cache\Exception\UnsupportedCacheException',

    'JSession' => 'use Joomla\CMS\Session\Session',

    'JUser'       => 'use Joomla\CMS\User\User',
    'JUserHelper' => 'use Joomla\CMS\User\UserHelper',

    'JForm'       => 'use Joomla\CMS\Form\Form',
    'JFormField'  => 'use Joomla\CMS\Form\FormField',
    'JFormHelper' => 'use Joomla\CMS\Form\FormHelper',
    'JFormRule'   => 'use Joomla\CMS\Form\FormRule',

    'JFormFieldAccessLevel'           => 'use Joomla\CMS\Form\Field\AccesslevelField',
    'JFormFieldAliastag'              => 'use Joomla\CMS\Form\Field\AliastagField',
    'JFormFieldAuthor'                => 'use Joomla\CMS\Form\Field\AuthorField',
    'JFormFieldCacheHandler'          => 'use Joomla\CMS\Form\Field\CachehandlerField',
    'JFormFieldCalendar'              => 'use Joomla\CMS\Form\Field\CalendarField',
    'JFormFieldCaptcha'               => 'use Joomla\CMS\Form\Field\CaptchaField',
    'JFormFieldCategory'              => 'use Joomla\CMS\Form\Field\CategoryField',
    'JFormFieldCheckbox'              => 'use Joomla\CMS\Form\Field\CheckboxField',
    'JFormFieldCheckboxes'            => 'use Joomla\CMS\Form\Field\CheckboxesField',
    'JFormFieldChromeStyle'           => 'use Joomla\CMS\Form\Field\ChromestyleField',
    'JFormFieldColor'                 => 'use Joomla\CMS\Form\Field\ColorField',
    'JFormFieldCombo'                 => 'use Joomla\CMS\Form\Field\ComboField',
    'JFormFieldComponentlayout'       => 'use Joomla\CMS\Form\Field\ComponentlayoutField',
    'JFormFieldComponents'            => 'use Joomla\CMS\Form\Field\ComponentsField',
    'JFormFieldContenthistory'        => 'use Joomla\CMS\Form\Field\ContenthistoryField',
    'JFormFieldContentlanguage'       => 'use Joomla\CMS\Form\Field\ContentlanguageField',
    'JFormFieldContenttype'           => 'use Joomla\CMS\Form\Field\ContenttypeField',
    'JFormFieldDatabaseConnection'    => 'use Joomla\CMS\Form\Field\DatabaseconnectionField',
    'JFormFieldEditor'                => 'use Joomla\CMS\Form\Field\EditorField',
    'JFormFieldEMail'                 => 'use Joomla\CMS\Form\Field\EmailField',
    'JFormFieldFile'                  => 'use Joomla\CMS\Form\Field\FileField',
    'JFormFieldFileList'              => 'use Joomla\CMS\Form\Field\FilelistField',
    'JFormFieldFolderList'            => 'use Joomla\CMS\Form\Field\FolderlistField',
    'JFormFieldFrontend_Language'     => 'use Joomla\CMS\Form\Field\FrontendlanguageField',
    'JFormFieldGroupedList'           => 'use Joomla\CMS\Form\Field\GroupedlistField',
    'JFormFieldHeadertag'             => 'use Joomla\CMS\Form\Field\HeadertagField',
    'JFormFieldHidden'                => 'use Joomla\CMS\Form\Field\HiddenField',
    'JFormFieldImageList'             => 'use Joomla\CMS\Form\Field\ImagelistField',
    'JFormFieldInteger'               => 'use Joomla\CMS\Form\Field\IntegerField',
    'JFormFieldLanguage'              => 'use Joomla\CMS\Form\Field\LanguageField',
    'JFormFieldLastvisitDateRange'    => 'use Joomla\CMS\Form\Field\LastvisitdaterangeField',
    'JFormFieldLimitbox'              => 'use Joomla\CMS\Form\Field\LimitboxField',
    'JFormFieldList'                  => 'use Joomla\CMS\Form\Field\ListField',
    'JFormFieldMedia'                 => 'use Joomla\CMS\Form\Field\MediaField',
    'JFormFieldMenu'                  => 'use Joomla\CMS\Form\Field\MenuField',
    'JFormFieldMenuitem'              => 'use Joomla\CMS\Form\Field\MenuitemField',
    'JFormFieldMeter'                 => 'use Joomla\CMS\Form\Field\MeterField',
    'JFormFieldModulelayout'          => 'use Joomla\CMS\Form\Field\ModulelayoutField',
    'JFormFieldModuleOrder'           => 'use Joomla\CMS\Form\Field\ModuleorderField',
    'JFormFieldModulePosition'        => 'use Joomla\CMS\Form\Field\ModulepositionField',
    'JFormFieldModuletag'             => 'use Joomla\CMS\Form\Field\ModuletagField',
    'JFormFieldNote'                  => 'use Joomla\CMS\Form\Field\NoteField',
    'JFormFieldNumber'                => 'use Joomla\CMS\Form\Field\NumberField',
    'JFormFieldOrdering'              => 'use Joomla\CMS\Form\Field\OrderingField',
    'JFormFieldPassword'              => 'use Joomla\CMS\Form\Field\PasswordField',
    'JFormFieldPlugins'               => 'use Joomla\CMS\Form\Field\PluginsField',
    'JFormFieldPlugin_Status'         => 'use Joomla\CMS\Form\Field\PluginstatusField',
    'JFormFieldPredefinedList'        => 'use Joomla\CMS\Form\Field\PredefinedListField',
    'JFormFieldRadio'                 => 'use Joomla\CMS\Form\Field\RadioField',
    'JFormFieldRange'                 => 'use Joomla\CMS\Form\Field\RangeField',
    'JFormFieldRedirect_Status'       => 'use Joomla\CMS\Form\Field\RedirectStatusField',
    'JFormFieldRegistrationDateRange' => 'use Joomla\CMS\Form\Field\RegistrationdaterangeField',
    'JFormFieldRules'                 => 'use Joomla\CMS\Form\Field\RulesField',
    'JFormFieldSessionHandler'        => 'use Joomla\CMS\Form\Field\SessionhandlerField',
    'JFormFieldSpacer'                => 'use Joomla\CMS\Form\Field\SpacerField',
    'JFormFieldSQL'                   => 'use Joomla\CMS\Form\Field\SqlField',
    'JFormFieldStatus'                => 'use Joomla\CMS\Form\Field\StatusField',
    'JFormFieldSubform'               => 'use Joomla\CMS\Form\Field\SubformField',
    'JFormFieldTag'                   => 'use Joomla\CMS\Form\Field\TagField',
    'JFormFieldTel'                   => 'use Joomla\CMS\Form\Field\TelephoneField',
    'JFormFieldTemplatestyle'         => 'use Joomla\CMS\Form\Field\TemplatestyleField',
    'JFormFieldText'                  => 'use Joomla\CMS\Form\Field\TextField',
    'JFormFieldTextarea'              => 'use Joomla\CMS\Form\Field\TextareaField',
    'JFormFieldTimezone'              => 'use Joomla\CMS\Form\Field\TimezoneField',
    'JFormFieldUrl'                   => 'use Joomla\CMS\Form\Field\UrlField',
    'JFormFieldUserActive'            => 'use Joomla\CMS\Form\Field\UseractiveField',
    'JFormFieldUserGroupList'         => 'use Joomla\CMS\Form\Field\UsergrouplistField',
    'JFormFieldUserState'             => 'use Joomla\CMS\Form\Field\UserstateField',
    'JFormFieldUser'                  => 'use Joomla\CMS\Form\Field\UserField',
    'JFormRuleBoolean'                => 'use Joomla\CMS\Form\Rule\BooleanRule',
    'JFormRuleCalendar'               => 'use Joomla\CMS\Form\Rule\CalendarRule',
    'JFormRuleCaptcha'                => 'use Joomla\CMS\Form\Rule\CaptchaRule',
    'JFormRuleColor'                  => 'use Joomla\CMS\Form\Rule\ColorRule',
    'JFormRuleEmail'                  => 'use Joomla\CMS\Form\Rule\EmailRule',
    'JFormRuleEquals'                 => 'use Joomla\CMS\Form\Rule\EqualsRule',
    'JFormRuleNotequals'              => 'use Joomla\CMS\Form\Rule\NotequalsRule',
    'JFormRuleNumber'                 => 'use Joomla\CMS\Form\Rule\NumberRule',
    'JFormRuleOptions'                => 'use Joomla\CMS\Form\Rule\OptionsRule',
    'JFormRulePassword'               => 'use Joomla\CMS\Form\Rule\PasswordRule',
    'JFormRuleRules'                  => 'use Joomla\CMS\Form\Rule\RulesRule',
    'JFormRuleTel'                    => 'use Joomla\CMS\Form\Rule\TelRule',
    'JFormRuleUrl'                    => 'use Joomla\CMS\Form\Rule\UrlRule',
    'JFormRuleUsername'               => 'use Joomla\CMS\Form\Rule\UsernameRule',

    'JMicrodata' => 'use Joomla\CMS\Microdata\Microdata',

    'JDatabaseDriver'               => 'use Joomla\Database\DatabaseDriver',
    'JDatabaseExporter'             => 'use Joomla\Database\DatabaseExporter',
    'JDatabaseFactory'              => 'use Joomla\Database\DatabaseFactory',
    'JDatabaseImporter'             => 'use Joomla\Database\DatabaseImporter',
    'JDatabaseInterface'            => 'use Joomla\Database\DatabaseInterface',
    'JDatabaseIterator'             => 'use Joomla\Database\DatabaseIterator',
    'JDatabaseQuery'                => 'use Joomla\Database\DatabaseQuery',
    'JDatabaseDriverMysqli'         => 'use Joomla\Database\Mysqli\MysqliDriver',
    'JDatabaseDriverPdo'            => 'use Joomla\Database\Pdo\PdoDriver',
    'JDatabaseDriverPdomysql'       => 'use Joomla\Database\Mysql\MysqlDriver',
    'JDatabaseDriverPgsql'          => 'use Joomla\Database\Pgsql\PgsqlDriver',
    'JDatabaseDriverSqlazure'       => 'use Joomla\Database\Sqlazure\SqlazureDriver',
    'JDatabaseDriverSqlite'         => 'use Joomla\Database\Sqlite\SqliteDriver',
    'JDatabaseDriverSqlsrv'         => 'use Joomla\Database\Sqlsrv\SqlsrvDriver',
    'JDatabaseExceptionConnecting'  => 'use Joomla\Database\Exception\ConnectionFailureException',
    'JDatabaseExceptionExecuting'   => 'use Joomla\Database\Exception\ExecutionFailureException',
    'JDatabaseExceptionUnsupported' => 'use Joomla\Database\Exception\UnsupportedAdapterException',
    'JDatabaseExporterMysqli'       => 'use Joomla\Database\Mysqli\MysqliExporter',
    'JDatabaseExporterPdomysql'     => 'use Joomla\Database\Mysql\MysqlExporter',
    'JDatabaseExporterPgsql'        => 'use Joomla\Database\Pgsql\PgsqlExporter',
    'JDatabaseImporterMysqli'       => 'use Joomla\Database\Mysqli\MysqliImporter',
    'JDatabaseImporterPdomysql'     => 'use Joomla\Database\Mysql\MysqlImporter',
    'JDatabaseImporterPgsql'        => 'use Joomla\Database\Pgsql\PgsqlImporter',
    'JDatabaseQueryElement'         => 'use Joomla\Database\Query\QueryElement',
    'JDatabaseQueryLimitable'       => 'use Joomla\Database\Query\LimitableInterface',
    'JDatabaseQueryPreparable'      => 'use Joomla\Database\Query\PreparableInterface',
    'JDatabaseQueryMysqli'          => 'use Joomla\Database\Mysqli\MysqliQuery',
    'JDatabaseQueryPdo'             => 'use Joomla\Database\Pdo\PdoQuery',
    'JDatabaseQueryPdomysql'        => 'use Joomla\Database\Mysql\MysqlQuery',
    'JDatabaseQueryPgsql'           => 'use Joomla\Database\Pgsql\PgsqlQuery',
    'JDatabaseQuerySqlazure'        => 'use Joomla\Database\Sqlazure\SqlazureQuery',
    'JDatabaseQuerySqlite'          => 'use Joomla\Database\Sqlite\SqliteQuery',
    'JDatabaseQuerySqlsrv'          => 'use Joomla\Database\Sqlsrv\SqlsrvQuery',

    'JFactory' => 'use Joomla\CMS\Factory',

    'JMail'       => 'use Joomla\CMS\Mail\Mail',
    'JMailHelper' => 'use Joomla\CMS\Mail\MailHelper',

    'JClientHelper' => 'use Joomla\CMS\Client\ClientHelper',
    'JClientFtp'    => 'use Joomla\CMS\Client\FtpClient',
    'JFTP'          => 'use Joomla\CMS\Client\FtpClient',

    'JUpdate'            => 'use Joomla\CMS\Updater\Update',
    'JUpdateAdapter'     => 'use Joomla\CMS\Updater\UpdateAdapter',
    'JUpdater'           => 'use Joomla\CMS\Updater\Updater',
    'JUpdaterCollection' => 'use Joomla\CMS\Updater\Adapter\CollectionAdapter',
    'JUpdaterExtension'  => 'use Joomla\CMS\Updater\Adapter\ExtensionAdapter',

    'JCrypt'                                => 'use Joomla\CMS\Crypt\Crypt',
    'JCryptCipher'                          => 'use Joomla\Crypt\CipherInterface',
    'JCryptKey'                             => 'use Joomla\Crypt\Key',
    'use Joomla\CMS\Crypt\CipherInterface' => 'use Joomla\Crypt\CipherInterface',
    'use Joomla\CMS\Crypt\Key'             => 'use Joomla\Crypt\Key',
    'JCryptCipherCrypto'                    => 'use Joomla\CMS\Crypt\Cipher\CryptoCipher',

    'JStringPunycode' => 'use Joomla\CMS\String\PunycodeHelper',

    'JBuffer'  => 'use Joomla\CMS\Utility\BufferStreamHandler',
    'JUtility' => 'use Joomla\CMS\Utility\Utility',

    'JInputCli'    => 'use Joomla\CMS\Input\Cli',
    'JInputCookie' => 'use Joomla\CMS\Input\Cookie',
    'JInputFiles'  => 'use Joomla\CMS\Input\Files',
    'JInput'       => 'use Joomla\CMS\Input\Input',
    'JInputJSON'   => 'use Joomla\CMS\Input\Json',

    'JFeed'                => 'use Joomla\CMS\Feed\Feed',
    'JFeedEntry'           => 'use Joomla\CMS\Feed\FeedEntry',
    'JFeedFactory'         => 'use Joomla\CMS\Feed\FeedFactory',
    'JFeedLink'            => 'use Joomla\CMS\Feed\FeedLink',
    'JFeedParser'          => 'use Joomla\CMS\Feed\FeedParser',
    'JFeedPerson'          => 'use Joomla\CMS\Feed\FeedPerson',
    'JFeedParserAtom'      => 'use Joomla\CMS\Feed\Parser\AtomParser',
    'JFeedParserNamespace' => 'use Joomla\CMS\Feed\Parser\NamespaceParserInterface',
    'JFeedParserRss'       => 'use Joomla\CMS\Feed\Parser\RssParser',
    'JFeedParserRssItunes' => 'use Joomla\CMS\Feed\Parser\Rss\ItunesRssParser',
    'JFeedParserRssMedia'  => 'use Joomla\CMS\Feed\Parser\Rss\MediaRssParser',

    'JImage'                     => 'use Joomla\CMS\Image\Image',
    'JImageFilter'               => 'use Joomla\CMS\Image\ImageFilter',
    'JImageFilterBackgroundfill' => 'use Joomla\CMS\Image\Filter\Backgroundfill',
    'JImageFilterBrightness'     => 'use Joomla\CMS\Image\Filter\Brightness',
    'JImageFilterContrast'       => 'use Joomla\CMS\Image\Filter\Contrast',
    'JImageFilterEdgedetect'     => 'use Joomla\CMS\Image\Filter\Edgedetect',
    'JImageFilterEmboss'         => 'use Joomla\CMS\Image\Filter\Emboss',
    'JImageFilterNegate'         => 'use Joomla\CMS\Image\Filter\Negate',
    'JImageFilterSmooth'         => 'use Joomla\CMS\Image\Filter\Smooth',

    'JObject' => 'use Joomla\CMS\Object\CMSObject',

    'JExtensionHelper' => 'use Joomla\CMS\Extension\ExtensionHelper',

    'JHtml' => 'use Joomla\CMS\HTML\HTMLHelper',

    'use Joomla\Application\Cli\CliInput'                              => 'use Joomla\CMS\Application\CLI\CliInput',
    'use Joomla\Application\Cli\CliOutput'                             => 'use Joomla\CMS\Application\CLI\CliOutput',
    'use Joomla\Application\Cli\ColorStyle'                            => 'use Joomla\CMS\Application\CLI\ColorStyle',
    'use Joomla\Application\Cli\Output\Stdout'                        => 'use Joomla\CMS\Application\CLI\Output\Stdout',
    'use Joomla\Application\Cli\Output\Xml'                           => 'use Joomla\CMS\Application\CLI\Output\Xml',
    'use Joomla\Application\Cli\Output\Processor\ColorProcessor'     => 'use Joomla\CMS\Application\CLI\Output\Processor\ColorProcessor',
    'use Joomla\Application\Cli\Output\Processor\ProcessorInterface' => 'use Joomla\CMS\Application\CLI\Output\Processor\ProcessorInterface',

    'JFile'              => 'use Joomla\CMS\Filesystem\File',
    'JFolder'            => 'use Joomla\CMS\Filesystem\Folder',
    'JFilesystemHelper'  => 'use Joomla\CMS\Filesystem\FilesystemHelper',
    'JFilesystemPatcher' => 'use Joomla\CMS\Filesystem\Patcher',
    'JPath'              => 'use Joomla\CMS\Filesystem\Path',
    'JStream'            => 'use Joomla\CMS\Filesystem\Stream',
    'JStreamString'      => 'use Joomla\CMS\Filesystem\Streams\StreamString',
    'JStringController'  => 'use Joomla\CMS\Filesystem\Support\StringController',

    'JClassLoader' => 'use Joomla\CMS\Autoload\ClassLoader',

    'JFormFilterInt_Array' => 'use Joomla\CMS\Form\Filter\IntarrayFilter',

    'JAdapter'         => 'use Joomla\CMS\Adapter\Adapter',
    'JAdapterInstance' => 'use Joomla\CMS\Adapter\AdapterInstance',

    'JHtmlAccess'          => 'use Joomla\CMS\HTML\Helpers\Access',
    'JHtmlActionsDropdown' => 'use Joomla\CMS\HTML\Helpers\ActionsDropdown',
    'JHtmlAdminLanguage'   => 'use Joomla\CMS\HTML\Helpers\AdminLanguage',
    'JHtmlBehavior'        => 'use Joomla\CMS\HTML\Helpers\Behavior',
    'JHtmlBootstrap'       => 'use Joomla\CMS\HTML\Helpers\Bootstrap',
    'JHtmlCategory'        => 'use Joomla\CMS\HTML\Helpers\Category',
    'JHtmlContent'         => 'use Joomla\CMS\HTML\Helpers\Content',
    'JHtmlContentlanguage' => 'use Joomla\CMS\HTML\Helpers\ContentLanguage',
    'JHtmlDate'            => 'use Joomla\CMS\HTML\Helpers\Date',
    'JHtmlDebug'           => 'use Joomla\CMS\HTML\Helpers\Debug',
    'JHtmlDraggablelist'   => 'use Joomla\CMS\HTML\Helpers\DraggableList',
    'JHtmlDropdown'        => 'use Joomla\CMS\HTML\Helpers\Dropdown',
    'JHtmlEmail'           => 'use Joomla\CMS\HTML\Helpers\Email',
    'JHtmlForm'            => 'use Joomla\CMS\HTML\Helpers\Form',
    'JHtmlFormbehavior'    => 'use Joomla\CMS\HTML\Helpers\FormBehavior',
    'JHtmlGrid'            => 'use Joomla\CMS\HTML\Helpers\Grid',
    'JHtmlIcons'           => 'use Joomla\CMS\HTML\Helpers\Icons',
    'JHtmlJGrid'           => 'use Joomla\CMS\HTML\Helpers\JGrid',
    'JHtmlJquery'          => 'use Joomla\CMS\HTML\Helpers\Jquery',
    'JHtmlLinks'           => 'use Joomla\CMS\HTML\Helpers\Links',
    'JHtmlList'            => 'use Joomla\CMS\HTML\Helpers\ListHelper',
    'JHtmlMenu'            => 'use Joomla\CMS\HTML\Helpers\Menu',
    'JHtmlNumber'          => 'use Joomla\CMS\HTML\Helpers\Number',
    'JHtmlSearchtools'     => 'use Joomla\CMS\HTML\Helpers\SearchTools',
    'JHtmlSelect'          => 'use Joomla\CMS\HTML\Helpers\Select',
    'JHtmlSidebar'         => 'use Joomla\CMS\HTML\Helpers\Sidebar',
    'JHtmlSortableList'    => 'use Joomla\CMS\HTML\Helpers\SortableList',
    'JHtmlString'          => 'use Joomla\CMS\HTML\Helpers\StringHelper',
    'JHtmlTag'             => 'use Joomla\CMS\HTML\Helpers\Tag',
    'JHtmlTel'             => 'use Joomla\CMS\HTML\Helpers\Telephone',
    'JHtmlUser'            => 'use Joomla\CMS\HTML\Helpers\User',


// Class map of the core extensions
/*
    'ActionLogPlugin'      => 'use Joomla\Component\Actionlogs\Administrator\Plugin\ActionLogPlugin',

    'FieldsPlugin'     => 'use Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin',
    'FieldsListPlugin' => 'use Joomla\Component\Fields\Administrator\Plugin\FieldsListPlugin',

    'PrivacyExportDomain'  => 'use Joomla\Component\Privacy\Administrator\Export\Domain',
    'PrivacyExportField'   => 'use Joomla\Component\Privacy\Administrator\Export\Field',
    'PrivacyExportItem'    => 'use Joomla\Component\Privacy\Administrator\Export\Item',
    'PrivacyPlugin'        => 'use Joomla\Component\Privacy\Administrator\Plugin\PrivacyPlugin',
    'PrivacyRemovalStatus' => 'use Joomla\Component\Privacy\Administrator\Removal\Status',
    'PrivacyTableRequest'  => 'use Joomla\Component\Privacy\Administrator\Table\RequestTable',

    'TagsTableTag' => 'use Joomla\Component\Tags\Administrator\Table\TagTable',

    'ContentHelperRoute' => 'use Joomla\Component\Content\Site\Helper\RouteHelper',

    'FinderIndexerAdapter'  => 'use Joomla\Component\Finder\Administrator\Indexer\Adapter',
    'FinderIndexerHelper'   => 'use Joomla\Component\Finder\Administrator\Indexer\Helper',
    'FinderIndexer'         => 'use Joomla\Component\Finder\Administrator\Indexer\Indexer',
    'FinderIndexerParser'   => 'use Joomla\Component\Finder\Administrator\Indexer\Parser',
    'FinderIndexerQuery'    => 'use Joomla\Component\Finder\Administrator\Indexer\Query',
    'FinderIndexerResult'   => 'use Joomla\Component\Finder\Administrator\Indexer\Result',
    'FinderIndexerTaxonomy' => 'use Joomla\Component\Finder\Administrator\Indexer\Taxonomy',
    'FinderIndexerToken'    => 'use Joomla\Component\Finder\Administrator\Indexer\Token',
*/
];

// Function to search for deprecated functions in a file
function searchDeprecatedFunctions($filePath, $deprecatedIdentifiers) {
    $fileContents = file_get_contents($filePath);
    foreach ($deprecatedIdentifiers as $identifier => $alias) {
// Create a regex pattern to match the identifier in various contexts
        $pattern = '/\b(?:' . preg_quote($identifier, '/')
                   . '|\w+::' . preg_quote($identifier, '/')
                   . '|\buse\s+' . preg_quote($identifier, '/')
                   . '|\bnew\s+' . preg_quote($identifier, '/')
                   . '|\bextends\s+' . preg_quote($identifier, '/') . ')\b/';
        if (preg_match($pattern, $fileContents)) {
            if (stripos($fileContents, $alias) === false)
            {
                echo "Deprecated identifier (with no use clause) '$identifier' found in file: $filePath\n";
            }
            else
            {
                echo "Deprecated identifier '$identifier' found in file: $filePath\n";
            }
        }
    }
}

// Function to recursively search through directories
function searchDirectory($dir, $deprecatedIdentifiers) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $filePath = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($filePath)) {
            searchDirectory($filePath, $deprecatedIdentifiers);
        } elseif (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
            searchDeprecatedFunctions($filePath, $deprecatedIdentifiers);
        }
    }
}

// Start searching from a specific directories
$searchFirectories = [
    'component',
    'libraries',
    'modules',
    'package',
    'plugins',
    //'templates',
];

foreach ( $searchFirectories as $searchFirectory )
{
    searchDirectory($searchFirectory, $deprecatedIdentifiers);
}
