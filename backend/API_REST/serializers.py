from API_REST.models import User

from django.contrib.auth import update_session_auth_hash

from rest_framework import serializers


class UserSerializer(serializers.ModelSerializer):

    
    class Meta:
        model = User
        fields = ('username', 'first_name', 'last_name', 'email', 'password')



    def create(self, validated_data):
        user = User(
            email=validated_data['email'],
            username=validated_data['username']
        )
        user.set_password(validated_data['password'])
        user.save()
        return user

    def validate(self, data):
        '''
        Ensure the passwords are the same
        '''
        if data['password']:
            print "Here"
            if data['password'] != data['confirm_password']:
                raise serializers.ValidationError(
                    "The passwords have to be the same"
                )
        return data