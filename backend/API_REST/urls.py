from rest_framework_jwt.views import obtain_jwt_token, refresh_jwt_token, verify_jwt_token

from API_REST.views import StudentViewSet, StudentRegister, CourseViewSet
from API_REST import views

from django.conf.urls import url, include

from rest_framework.urlpatterns import format_suffix_patterns
from rest_framework import renderers



student_list = StudentViewSet.as_view({
    'get': 'list',
})

student_detail = StudentViewSet.as_view({
    'get': 'retrieve',
    'put': 'update',
})

student_register = StudentRegister.as_view({
    'post': 'create',
    'get': 'list'
})

course_list = CourseViewSet.as_view({
    'post': 'create',
    'get': 'list'
})

urlpatterns = [
    url(r'^login', obtain_jwt_token),
    url(r'^token-refresh/', refresh_jwt_token),
    url(r'^token-verify/', verify_jwt_token),

    url(r'^students', student_list, name='user-list'),
    url(r'^student/(?P<pk>[0-9]+)/$', student_detail, name='student_detail'),
    url(r'^register', student_register, name='student-register'),

    url(r'^course', course_list, name='course-list'),
    
    # url(r'^users/new/$', user_create),
    # url(r'^users/(?P<pk>[0-9]+)/$', user_detail, name='user-detail'),

]

urlpatterns = format_suffix_patterns(urlpatterns)
