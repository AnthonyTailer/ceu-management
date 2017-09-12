# -*- coding: utf-8 -*-
from API_REST.models import User
from API_REST.serializers import UserSerializer
from rest_framework import viewsets
from rest_framework.permissions import IsAuthenticated



class UserViewSet(viewsets.ModelViewSet):
    """
    This viewset automatically provides `list` and `detail` actions.
    """
    queryset = User.objects.all()
    permission_classes = (IsAuthenticated, )
    serializer_class = UserSerializer
