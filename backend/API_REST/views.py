# -*- coding: utf-8 -*-
from API_REST.models import Student, Course
from API_REST.serializers import StudentSerializer, CourseSerializer
from rest_framework import viewsets
from rest_framework.views import APIView
from rest_framework.permissions import IsAuthenticated, AllowAny


class StudentViewSet(viewsets.ModelViewSet):
	"""
	This viewset automatically provides `list` and `detail` actions.
	"""
	permission_classes = (IsAuthenticated, )
	queryset = Student.objects.all()
	serializer_class = StudentSerializer

class StudentRegister(viewsets.ModelViewSet):
	"""
	Used to register a new student.
	"""

	permission_classes = (AllowAny,)
	queryset = Student.objects.all()
	serializer_class = StudentSerializer


class CourseViewSet(viewsets.ModelViewSet):
	permission_classes = (AllowAny,)
	queryset = Course.objects.all()
	serializer_class = CourseSerializer


	


def jwt_response_payload_handler(token, student=None, request=None):
    return {
        'token': token,
        'student': StudentSerializer(student, context={'request': request}).data
    }