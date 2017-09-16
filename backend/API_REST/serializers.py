from API_REST.models import Student, Course

from django.contrib.auth import update_session_auth_hash

from rest_framework import serializers


class CourseSerializer(serializers.ModelSerializer):

    class Meta:
        model = Course
        fields = ('courseName', 'duration', '_type')

class StudentSerializer(serializers.ModelSerializer):
    confirm_password = serializers.CharField(write_only=True)

    class Meta:
        model = Student
        fields = ('fullName', 'registration','id_course','age','email', 'rg', 'cpf', 'phone1', 'password', 'confirm_password')



    def create(self, validated_data):
        student = Student(
            email=validated_data['email'],
            registration=validated_data['registration'],
            phone1=validated_data['phone1'],
            rg=validated_data['rg'],
            cpf=validated_data['cpf'],
            fullName=validated_data['fullName'],
            age=validated_data['age'],
            id_course=validated_data['id_course'],
        )
        student.set_password(validated_data['password'])
        student.save()
        return student

    def validate(self, data):
        '''
        Ensure the passwords are the same
        '''
        print(data)
        if data['password']:
            print ("Here")
            if data['password'] != data['confirm_password']:
                raise serializers.ValidationError(
                    "The passwords have to be the same"
                )
        return data

