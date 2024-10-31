#! /bin/bash
# See https://github.com/GaryJones/wordpress-plugin-git-flow-svn-deploy for instructions and credits.

echo
echo "WordPress Plugin Git-Flow SVN Deploy v1.0.0-dev"
echo
echo "Step 1. Let's collect some information first."
echo
echo "Default values are in brackets - just hit enter to accept them."
echo

# Get some user input
# Can't use the -i flag for read, since that doesn't work for bash 3
read -e -p "1a) WordPress Repo Plugin Slug e.g. press-pay: " PLUGINSLUG
echo

# Set up some default values. Feel free to change these in your own script
CURRENTDIR=`pwd`
default_svnpath="/Users/spinlock/WordPress/Subversion/$PLUGINSLUG"
default_svnurl="http://plugins.svn.wordpress.org/$PLUGINSLUG"
default_svnuser="presspay"
default_plugindir="$CURRENTDIR"
default_mainfile="$PLUGINSLUG.php"

echo "1b) Path to a local directory where a temporary SVN checkout can be made."
read -e -p "No trailing slash and don't add trunk ($default_svnpath): " input
SVNPATH="${input:-$default_svnpath}"
echo

echo "1c) Remote SVN repo on WordPress.org. No trailing slash."
read -e -p "($default_svnurl): " input
SVNURL="${input:-$default_svnurl}"
echo

read -e -p "1d) Your WordPress repo SVN username ($default_svnuser): " input
SVNUSER="${input:-$default_svnuser}"
echo

echo "1e) Your local plugin root directory, the Git repo."
read -e -p "($default_plugindir): " input
PLUGINDIR="${input:-$default_plugindir}"
echo

read -e -p "1f) Name of the main plugin file ($default_mainfile): " input
MAINFILE="${input:-$default_mainfile}"
echo

echo "That's all of the data collected."
echo
echo "Slug: $PLUGINSLUG"
echo "Temp checkout path: $SVNPATH"
echo "Remote SVN repo: $SVNURL"
echo "SVN username: $SVNUSER"
echo "Plugin directory: $PLUGINDIR"
echo "Main file: $MAINFILE"
echo

# git config
GITPATH="$PLUGINDIR/" # this file should be in the base of your git repository

# Let's begin...
echo ".........................................."
echo 
echo "Preparing to deploy WordPress plugin"
echo 
echo ".........................................."
echo 

# Check version in readme.txt is the same as plugin file after translating both to unix line breaks to work around grep's failure to identify mac line breaks
NEWVERSION1=`grep "^Stable tag:" $GITPATH/readme.txt | awk -F' ' '{print $NF}' | tr -d '\r'`
echo "readme.txt version: $NEWVERSION1"
NEWVERSION2=`grep "Version:" $GITPATH/$MAINFILE | awk -F' ' '{print $NF}' | tr -d '\r'`
echo "$MAINFILE version: $NEWVERSION2"

if [ "$NEWVERSION1" != "$NEWVERSION2" ]; then echo "Version in readme.txt & $MAINFILE don't match. Exiting...."; exit 1; fi

echo "Versions match in readme.txt and $MAINFILE. Let's proceed..."

echo "Changing to $GITPATH"
cd $GITPATH

echo
echo "Updating local copy of SVN repo trunk ..."
svn up $SVNPATH

echo "Ignoring GitHub specific files"
svn propset svn:ignore "README.md
Thumbs.db
.git
.gitignore" "$SVNPATH"

echo "Exporting the HEAD of master from git to the trunk of SVN"
git checkout-index -a -f --prefix=$SVNPATH/trunk/

# If submodule exist, recursively check out their indexes
if [ -f ".gitmodules" ]
	then
		echo "Exporting the HEAD of each submodule from git to the trunk of SVN"
		git submodule init
		git submodule update
		git submodule foreach --recursive 'git checkout-index -a -f --prefix=$SVNPATH/trunk/$path/'
fi

echo "Changing directory to SVN and committing to trunk"
cd $SVNPATH/trunk/
# Delete all files that should not now be added.
# Skip this step to obfuscate the svn repo.
#svn status | grep -v "^.[ \t]*\..*" | grep "^\!" | awk '{print $2}' | xargs svn del
# Add all new files that are not set to be ignored
svn status | grep -v "^.[ \t]*\..*" | grep "^?" | awk '{print $2}' | xargs svn add
svn commit --username=$SVNUSER -m "Preparing for $NEWVERSION1 release"

echo "Creating new SVN tag and committing it"
cd $SVNPATH
svn copy trunk/ tags/$NEWVERSION1/
cd $SVNPATH/tags/$NEWVERSION1
#svn commit --username=$SVNUSER -m "Tagging version $NEWVERSION1"

echo "*** FIN ***"
