# -*- coding: utf-8 -*-
from API_REST.models import User
from API_REST.serializers import UserSerializer
from rest_framework import viewsets
from rest_framework.views import APIView
from rest_framework.permissions import IsAuthenticated, AllowAny


class UserViewSet(viewsets.ModelViewSet):
	"""
	This viewset automatically provides `list` and `detail` actions.
	"""
	permission_classes = (IsAuthenticated, )
	queryset = User.objects.all()
	serializer_class = UserSerializer

class UserRegister(viewsets.ModelViewSet):
	"""
	List all snippets, or create a new snippet.
	"""

	permission_classes = (AllowAny,)
	queryset = User.objects.all()
	serializer_class = UserSerializer
