from rest_framework_jwt.views import obtain_jwt_token, refresh_jwt_token, verify_jwt_token

from API_REST.views import UserViewSet, UserRegister
from API_REST import views

from django.conf.urls import url, include

from rest_framework.urlpatterns import format_suffix_patterns
from rest_framework import renderers



user_list = UserViewSet.as_view({
    'get': 'list',
})
'''user_detail = UserViewSet.as_view({d
    'get': 'retrieve',
    'post': 'create'
})
user_create = UserViewSet.as_view({
    'post': 'create'
})'''

user_register = UserRegister.as_view({
    'post': 'create'
})

urlpatterns = [
    url(r'^login/', obtain_jwt_token),
    url(r'^users', user_list, name='user-list'),
    url(r'^register', UserViewSet),
    url(r'^token-refresh/', refresh_jwt_token),
    url(r'^token-verify/', verify_jwt_token),
    # url(r'^users/new/$', user_create),
    # url(r'^users/(?P<pk>[0-9]+)/$', user_detail, name='user-detail'),

]

urlpatterns = format_suffix_patterns(urlpatterns)
