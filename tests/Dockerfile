FROM centos:centos6
MAINTAINER Pandora FMS Team <info@pandorafms.com>

RUN { \
	echo '[EPEL]'; \
	echo 'name = CentOS Epel'; \
	echo 'baseurl = http://dl.fedoraproject.org/pub/epel/6/x86_64'; \
	echo 'enabled=1'; \
	echo 'gpgcheck=0'; \
} > /etc/yum.repos.d/extra_repos.repo

RUN { \
        echo '[artica_pandorafms]'; \
        echo 'name=CentOS6 - PandoraFMS official repo'; \
        echo 'baseurl=http://artica.es/centos6'; \
        echo 'gpgcheck=0'; \
        echo 'enabled=1'; \
} > /etc/yum.repos.d/pandorafms.repo

RUN yum -y update; yum clean all;

# Generic dependencies
RUN yum install -y \
	firefox
	python-pip
	xorg-x11-server-Xvfb
RUN pip install pyvirtualdisplay
RUN pip install selenium

# Pandora FMS Console dependencies
RUN yum install -y \ 
	git \
	httpd \
	cronie \
	ntp \
	openldap \
	anytermd \
	nfdump \
	wget \
	curl \
	openldap \
	plymouth \
	xterm \
	php \ 
	php-gd \ 
	graphviz \ 
	php-mysql \ 
	php-pear-DB \ 
	php-pear \
	php-pdo \
	php-mbstring \ 
	php-ldap \ 
	php-snmp \ 
	php-ldap \ 
	php-common \ 
	php-zip \ 
	nmap \
	xprobe2 \
	mysql-server \
	mysql

# Pandora FMS Server dependencies
RUN yum install -y \ 
	git \
	cronie \
	ntp \
	wget \
	curl \
	xterm \
	postfix \
	wmic \
	perl-HTML-Tree \ 
	perl-DBI \ 
	perl-DBD-mysql \ 
	perl-libwww-perl \ 
	perl-XML-Simple \ 
	perl-XML-SAX \ 
	perl-NetAddr-IP \ 
	net-snmp \ 
	net-tools \ 
	perl-IO-Socket-INET6 \ 
	perl-Socket6 \ 
	nmap \ 
	sudo \ 
	xprobe2 \ 
	make \ 
	perl-CPAN \ 
	perl-JSON \ 
	net-snmp-perl \ 
	perl-Time-HiRes \ 
	perl-XML-Twig \ 
	perl-Encode-Locale \
	net-snmp \
	net-snmp-utils
