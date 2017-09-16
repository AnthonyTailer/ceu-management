# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.apps import AppConfig


class ApiRestConfig(AppConfig):
    name = 'API_REST'

	def ready(self):
        from . import signals