# -*- coding: utf-8 -*-
#
import os
import sys

from recommonmark.parser import CommonMarkParser

sys.path.insert(0, os.path.abspath('..'))
os.environ.setdefault("DJANGO_SETTINGS_MODULE", "readthedocs.settings.dev")

from django.conf import settings

import django
django.setup()


sys.path.append(os.path.abspath('_ext'))
extensions = [
    'sphinx.ext.autodoc',
    'sphinx.ext.intersphinx',
    'sphinxcontrib.httpdomain',
    'djangodocs',
]
templates_path = ['_templates']

source_suffix = ['.rst', '.md']
source_parsers = {
    '.md': CommonMarkParser,
}

master_doc = 'index'
project = u'LibreNMS'
copyright = u'LibreNMS Project and Contributors'
version = '1.0'
release = '1.0'
exclude_patterns = ['_build']
default_role = 'obj'
pygments_style = 'sphinx'
intersphinx_mapping = {
    'python': ('http://python.readthedocs.io/en/latest/', None),
    'django': ('http://django.readthedocs.io/en/1.8.x/', None),
    'sphinx': ('http://sphinx.readthedocs.io/en/latest/', None),
}
# This doesn't exist since we aren't shipping any static files ourselves.
#html_static_path = ['_static']
htmlhelp_basename = 'LibreNMSdoc'
latex_documents = [
    ('index', 'ReadTheDocs.tex', u'LibreNMS Documentation',
     u'laf', 'manual'),
]
man_pages = [
    ('index', 'rlibrenms', u'LibreNMS Documentation',
     [u'laf'], 1)
]

exclude_patterns = [
    # 'api' # needed for ``make gettext`` to not die.
]

language = 'en'

locale_dirs = [
    'locale/',
]
gettext_compact = False


import import sphinx_bootstrap_theme
html_theme = 'bootstrap'
html_theme_path = sphinx_bootstrap_theme.get_html_theme_path()

html_theme_options = {
    # Navigation bar title. (Default: ``project`` value)
    'navbar_title': "LibreNMS",

    # Tab name for entire site. (Default: "Site")
    'navbar_site_name': "LibreNMS",

    # A list of tuples containing pages or urls to link to.
    # Valid tuples should be in the following forms:
    #    (name, page)                 # a link to a page
    #    (name, "/aa/bb", 1)          # a link to an arbitrary relative url
    #    (name, "http://example.com", True) # arbitrary absolute url
    # Note the "1" or "True" value above as the third argument to indicate
    # an arbitrary url.
    'navbar_links': [
        ("Examples", "examples"),
        ("Link", "http://example.com", True),
    ],

    # Render the next and previous page links in navbar. (Default: true)
    'navbar_sidebarrel': True,

    # Render the current pages TOC in the navbar. (Default: true)
    'navbar_pagenav': True,

    # Tab name for the current pages TOC. (Default: "Page")
    'navbar_pagenav_name': "Page",

    # Global TOC depth for "site" navbar tab. (Default: 1)
    # Switching to -1 shows all levels.
    'globaltoc_depth': 2,

    # Include hidden TOCs in Site navbar?
    #
    # Note: If this is "false", you cannot have mixed ``:hidden:`` and
    # non-hidden ``toctree`` directives in the same page, or else the build
    # will break.
    #
    # Values: "true" (default) or "false"
    'globaltoc_includehidden': "true",

    # HTML navbar class (Default: "navbar") to attach to <div> element.
    # For black navbar, do "navbar navbar-inverse"
    'navbar_class': "navbar navbar-inverse",

    # Fix navigation bar to top of page?
    # Values: "true" (default) or "false"
    'navbar_fixed_top': "true",

    # Location of link to source.
    # Options are "nav" (default), "footer" or anything else to exclude.
    'source_link_position': "nav",

    # Bootswatch (http://bootswatch.com/) theme.
    #
    # Options are nothing (default) or the name of a valid theme
    # such as "amelia" or "cosmo".
    'bootswatch_theme': "united",

    # Choose Bootstrap version.
    # Values: "3" (default) or "2" (in quotes)
    'bootstrap_version': "3",
}
