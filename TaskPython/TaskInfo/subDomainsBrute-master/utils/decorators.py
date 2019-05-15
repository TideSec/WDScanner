#!/usr/bin/env python
# -*- coding: utf-8 -*-

import utils


class BaseDecorator(object):

    def __init__(self, wrapped):
        self.wrapped = wrapped

    def execute(self, *args, **kwargs):
        return self.wrapped.execute(*args, **kwargs)


class SubDomainListDecorator(BaseDecorator):

    def __init__(self, wrapped):
        super(SubDomainListDecorator, self).__init__(wrapped)

    def execute(self, *args, **kwargs):
        result_list = super(SubDomainListDecorator, self).execute(*args, **kwargs)
        result_dict = {}
        if result_list is not None:
            for i in result_list:
                result_dict[i] = utils.get_ip(i)
        return result_dict


class CrtCatcherDecorator(SubDomainListDecorator):

    def __init__(self, wrapped):
        super(CrtCatcherDecorator, self).__init__(wrapped)

    def execute(self, *args, **kwargs):
        result_dict = super(CrtCatcherDecorator, self).execute(*args, **kwargs)
        print 'crt catcher complete.'
        return result_dict


class SearchEngineCatcherDecorator(SubDomainListDecorator):

    def __init__(self, wrapped):
        super(SearchEngineCatcherDecorator, self).__init__(wrapped)

    def execute(self, *args, **kwargs):
        result_dict = super(SearchEngineCatcherDecorator, self).execute(*args, **kwargs)
        print 'search engine catcher complete.'
        return  result_dict


class SubDomainBruteDecorator(BaseDecorator):

    def __init__(self, wrapped):
        super(SubDomainBruteDecorator, self).__init__(wrapped)

    def execute(self, *args, **kwargs):
        result_dict = self.wrapped.run(*args, **kwargs)
        print 'sub domains brute complete.'
        return result_dict


class PageCatcherDecorator(SubDomainListDecorator):

    def __init__(self, wrapped):
        super(PageCatcherDecorator, self).__init__(wrapped)

    def execute(self, *args, **kwargs):
        result_dict = super(PageCatcherDecorator, self).execute(*args, **kwargs)
        print 'page cathcher complete.'
        return result_dict


class RecursiveCatcherDecorator(SubDomainListDecorator):

    def __init__(self, wrapped):
        super(RecursiveCatcherDecorator, self).__init__(wrapped)

    def execute(self, *args, **kwargs):
        result_dict = super(RecursiveCatcherDecorator, self).execute(*args, **kwargs)
        print 'recursive catcher complete.'
        return result_dict
