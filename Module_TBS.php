<?php
namespace GDO\TBS;

use GDO\Contact\Method\Form;
use GDO\Core\GDO_Module;
use GDO\Classic\Module_Classic;
use GDO\TBS\Install\InstallTBS;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\Date\GDT_Duration;
use GDO\DB\GDT_UInt;
use GDO\DB\GDT_Checkbox;
use GDO\Net\GDT_Url;
use GDO\UI\GDT_Card;
use GDO\UI\GDT_Container;
use GDO\DB\Query;
use GDO\Vote\Module_Vote;
use GDO\Core\GDT_Secret;
use GDO\Core\GDT_Array;
use GDO\Core\Application;
use GDO\Core\Method;
use GDO\Core\GDT_Template;
use GDO\Core\GDT_Response;
use GDO\Core\Website;

/**
 * TBS website revival as gdo6 module.
 * 
 * - Read the Import instructions
 * - Solution to crypto1 is ahdefjuklgrbdsegf
 * 
 * @TODO BBDecoder in Module_TBSBBMessage
 * 
 * @author gizmore
 * @license Property of Erik and TBS
 */
final class Module_TBS extends GDO_Module
{
    public $module_priority = 110;
    public $module_license = 'Properitary';
    
    public function isSiteModule() { return true; }
    public function getTheme() { return 'tbs'; }
    public function onLoadLanguage() { return $this->loadLanguage('lang/tbs'); }
    public function href_administrate_module() { return href('TBS', 'Admin'); }
    
    ##############
    ### Module ###
    ##############
    public function getDependencies()
    {
        return [
            'Country', 'Language', 'Contact',
            'Classic', 'Forum', 'News', 'Mibbit',
            'OnlineUsers', 'Profile', 'PM',
            'Login', 'Register', 'Recovery', 'Admin',
            'Favicon', 'FontAwesome', 'Captcha',
            'JQuery', 'JQueryAutocomplete',
            'TBSBBMessage', 'LoadOnClick',
            'Perf', 'Statistics', 'Python',
            'Forum', 'LoginAs',
        ];
    }
    
    public function getClasses()
    {
        return [
            GDO_TBS_Challenge::class,
            GDO_TBS_ChallengeSolveAttempt::class,
            GDO_TBS_ChallengeSolved::class,
            GDO_TBS_ChallengeSolvedCategory::class,
        ];
    }
    
    ##############
    ### Config ###
    ##############
    public function getConfig()
    {
        return [
            GDT_Duration::make('chall_solve_timeout')->initial('5m'),
            GDT_UInt::make('chall_solve_attempts')->initial('5'),
            GDT_Secret::make('chall_solver_user')->initial('gizmore3'),
            GDT_Secret::make('chall_solver_pass')->initial('11111111'),
            GDT_Secret::make('tbs_xauth_key')->initial('you wanna know this key? :)'),
        ];
    }
    public function cfgSolveTimeout() { return $this->getConfigValue('chall_solve_timeout'); }
    public function cfgSolveAttempts() { return $this->getConfigVar('chall_solve_attempts'); }
    public function cfgSolveUser() { return $this->getConfigVar('chall_solver_user'); }
    public function cfgSolvePass() { return $this->getConfigVar('chall_solver_pass'); }
    public function cfgXAuthKey() { return $this->getConfigVar('tbs_xauth_key'); }
    
    public function getUserSettings()
    {
        return [
            GDT_Checkbox::make('tbs_ranked')->initial('1')->notNull(),
            GDT_Url::make('tbs_website')->allowLocal(false)->allowExternal()->reachable(),
        ];
    }
    
    public function tutorialWWWPath()
    {
        return $this->wwwPath('tutorials/');
    }
    
    ############
    ### Init ###
    ############
    public function onInstall()
    {
        InstallTBS::onInstall();
    }
    
    public function onIncludeScripts()
    {
        if (Application::instance()->hasTheme('tbs'))
        {
            $this->addJavascript('js/tbs.js');
            $this->addCSS('css/gdo6-tbs.css');
            Module_Classic::instance()->addJavascript('js/gdo6-classic.js');
        }
    }

    ##############
    ### Render ###
    ##############
    /**
     * Get TBS Admin Section tabs.
     * @return \GDO\UI\GDT_Bar
     */
    public function barAdminTabs()
    {
        $tabs = GDT_Bar::make()->horizontal();
        
        $tabs->addField(GDT_Link::make('link_tbs_import')->href(href('TBS', 'ImportRealTBS')));
        $tabs->addField(GDT_Link::make('link_tbs_recalc')->href(href('TBS', 'RecalcPoints')));
        
        return $tabs;
    }
    
    public function rawIcon($path, $title='')
    {
        $path = $this->wwwPath("images/{$path}");
        $title = $title ? " title=\"{$title}\"" : $title;
        return sprintf('<img%s src="%s" alt="icon" />', $title, $path);
    }
    
    #############
    ### Hooks ###
    #############
    /**
     * Add fields to profile card.
     * @param GDO_User $user
     * @param GDT_Card $card
     */
    public function hookProfileCard(GDO_User $user, GDT_Card $card)
    {
        $card->addField($this->userSetting($user, 'tbs_website'));
        $card->addField($this->userSetting($user, 'tbs_ranked'));
    }
    
    public function hookProfileTemplate(GDO_User $user)
    {
        echo $this->php('profile.php', ['user' => $user]);
    }
    
    public function hookDecoratePostUser(GDT_Card $card, GDT_Container $cont, GDO_User $user)
    {
        # Add likes
        $likes = Module_Vote::instance()->userSettingVar($user, 'likes');
        $cont->addField(GDT_UInt::make()->initial($likes)->label('btn_likes'));
        
        # Add groupmaster icons
        $cont2 = GDT_Container::make()->horizontal()->addClass('badge-container');
        foreach (GDT_TBS_ChallengeCategory::$CATS as $category)
        {
            $cont2->addField(
                GDT_TBS_GroupmasterIcon::make()->category($category)->gdo($user)
            );
        }
        $cont->addField($cont2);
    }
    
    public function hookMethodQueryTable_Forum_Thread(Query $query)
    {
        $join = 'LEFT JOIN gdo_tbs_challengesolvedcategory AS csc ON csc_user=post_creator';
        $query->join($join);
    }
    
    public function hookUserActivated(GDO_User $user)
    {
        # Create scoring upon activation.
        GDO_TBS_ChallengeSolvedCategory::updateUser($user);
    }
    
    public function hookIgnoreDocsFiles(GDT_Array $ignore)
    {
        $ignore->data[] = 'GDO/TBS/challenges/**/*';
        $ignore->data[] = 'GDO/TBS/DUMP/**/*';
        $ignore->data[] = 'GDO/TBS/HIDDEN/**/*';
        $ignore->data[] = 'GDO/TBS/HIDDEN_EXAMPLE/**/*';
        $ignore->data[] = 'GDO/TBS/INPUT/**/*';
        $ignore->data[] = 'GDO/TBS/tutorials/**/*';
        $ignore->data[] = 'GDO/TBS/downloads/**/*';
        $ignore->data[] = 'GDO/TBS/scripts/**/*';
    }
    
    ##########################
    ### Contact Form Hooks ###
    ##########################
    public function hookBeforeExecute(Method $method, GDT_Response $response)
    {
        if ($method instanceof Form)
        {
            $this->addContactCSS();
            $response->addField(
                GDT_Template::make()->
                template('TBS', 'page/contact_before.php'));
        }
    }
    
    private function addContactCSS()
    {
        $css = <<<END
        dt {
          background-image: url('/files/images/backgrounds/headline2.png');
          background-color: #911501;
          color:#FFF;
          font-family: "Georgia", "Times New Roman", serif;
          font-size: 14px;
          line-height: 14px;
          font-weight: normal;
          padding:1px 0px 2px 4px;
          margin-top: 10px;
          border-style:solid;
          border-color:#000;
          border-width:1px;
        }
        dd {
          background-color: #CCB;
          border-color: #000;
          border-style: solid;
          border-width: 0px 1px 1px 1px;
          padding: 4px 8px 4px 4px;
          font-family: "Verdana",sans-serif;
          font-size: 12px;
          line-height: 150%;
          text-align: justify;
        }
        END;
        Website::addInlineCSS($css);
    }
    
    public function hookAfterExecute(Method $method, GDT_Response $response)
    {
        if ($method instanceof Form)
        {
            $response->addField(
                GDT_Template::make()->
                template('TBS', 'page/contact_after.php'));
        }
    }
    
}
