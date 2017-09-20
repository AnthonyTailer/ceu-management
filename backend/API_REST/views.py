# -*- coding: utf-8 -*-
from API_REST.models import *
from API_REST.serializers import *
from rest_framework import viewsets
from rest_framework import status
from rest_framework.response import Response
from rest_framework.permissions import IsAuthenticated, AllowAny, IsAdminUser


class StudentViewSet(viewsets.ModelViewSet):
	"""
	This viewset automatically provides `list` and `detail` actions.
	"""
	permission_classes = (IsAdminUser, )
	queryset = Student.objects.all()
	serializer_class = StudentSerializer


class StudentRegister(viewsets.ModelViewSet):
	"""
	Used to register a new student.
	"""



	permission_classes = (AllowAny,)
	queryset = Student.objects.all()
	serializer_class = StudentSerializer

	def create(self, request, *args, **kwargs):
		serializer = self.get_serializer(data=request.data, many=isinstance(request.data, list))
		serializer.is_valid(raise_exception=True)
		self.perform_create(serializer)
		headers = self.get_success_headers(serializer.data)
		return Response(serializer.data, status=status.HTTP_201_CREATED, headers=headers)


class CourseViewSet(viewsets.ModelViewSet):
	permission_classes = (AllowAny,)
	queryset = Course.objects.all()
	serializer_class = CourseSerializer

class BlockViewSet(viewsets.ModelViewSet):
	permission_classes = (AllowAny,)
	queryset = Block.objects.all()
	serializer_class = BlockSerializer
	
class AptoViewSet(viewsets.ModelViewSet):
	permissions_classes = (AllowAny,)
	queryset = Apartament.objects.all()
	serializer_class = AptoSerializer


def jwt_response_payload_handler(token, student=None, request=None):
    return {
        'token': token,
        'student': StudentSerializer(student, context={'request': request}).data
    }