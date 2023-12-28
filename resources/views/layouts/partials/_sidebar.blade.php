<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <div class="slimscroll-menu" id="remove-scroll">
        <!--- Side Menu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu" id="side-menu">
                <li class="menu-title">Main</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}" class="waves-effect">
                        <i class="fa fa-home"></i><span> Dashboard </span>
                    </a>
                </li>

                <li class="menu-title">Components</li>

                <li>
                    <a href="{{ route('admin.clients.index') }}" class="waves-effect">
                        <i class="fa fa-users"></i><span> Clients </span>
                    </a>
                </li>

                <li>
                    <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-location-arrow"></i><span>
                            Location
                            <span class="float-right menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                        </span></a>

                    <ul class="submenu">
                        <li><a href="{{ route('common.locations.countries.index') }}">Country</a></li>
                        <li><a href="{{ route('common.locations.states.index') }}">State</a></li>
                        <li><a href="{{ route('common.locations.cities.index') }}">City</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('admin.subs-packages.index') }}" class="waves-effect">
                        <i class="fas fa-rocket"></i><span> Subscription Package </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.subscriptions.invoices') }}" class="waves-effect">
                        <i class="fas fa-file-invoice-dollar"></i><span> Subscription Invoices </span>
                    </a>
                </li>

                <li>
                    <a href="javascript:void(0);" class="waves-effect"><i class="fas fa-hands-helping"></i><span> Help &
                            Contact
                            <span class="float-right menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                        </span></a>

                    <ul class="submenu">
                        <li>
                            <a href="{{ route('admin.issues.index') }}" class="waves-effect">
                                <span> Issues </span>
                            </a>
                            <a href="{{ route('admin.contacts.index') }}" class="waves-effect">
                                <span> Contacts </span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-question-circle"></i><span> FAQ
                            <span class="float-right menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                        </span></a>

                    <ul class="submenu">
                        <li>
                            <a href="{{ route('admin.faq-categories.index') }}" class="waves-effect">
                                <span> FAQ Category </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.faqs.index') }}" class="waves-effect">
                                <span> FAQ </span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-cog"></i><span> Settings
                            <span class="float-right menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                        </span></a>

                    <ul class="submenu">
                        <li>
                            <a href="{{ route('admin.system-settings.edit.general') }}" class="waves-effect">
                                <span> General Settings </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.system-settings.edit.api') }}" class="waves-effect">
                                <span> API Settings </span>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->