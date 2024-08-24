import sys
import pandas as pd
from sklearn.tree import DecisionTreeClassifier
from sklearn.model_selection import train_test_split
from sklearn import metrics
from sklearn.preprocessing import OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.tree import _tree
import matplotlib.pyplot as plt
from sklearn.tree import plot_tree
import json
import joblib  # Import joblib untuk menyimpan model

col_names = ['riwayat sebelum sma/ma', 'status', 'jarak tempuh', 'rata-rata nilai', 'alasan masuk ponpes', 'beasiswa', 'keputusan']
pima = pd.read_csv("uploads/dataset.csv", header=None, names=col_names)

# Hapus baris dengan nilai "KEPUTUSAN" di kolom 'keputusan'
pima = pima[pima['keputusan'] != 'KEPUTUSAN']

#split dataset in features and target variable
feature_cols = ['riwayat sebelum sma/ma', 'status', 'jarak tempuh', 'rata-rata nilai', 'alasan masuk ponpes', 'beasiswa']
X = pima[feature_cols]
y = pima.keputusan

# One-Hot Encoding menggunakan ColumnTransformer
transformer = ColumnTransformer(
    transformers=[('onehot', OneHotEncoder(), feature_cols)],
    remainder='passthrough'  # Biarkan kolom numerik tidak berubah
)
X = transformer.fit_transform(X)

# Get feature names after OneHotEncoding
# This will give you a list of all features after the transformation
feature_names_after_encoding = transformer.get_feature_names_out(feature_cols)

# Split dataset into training set and test set
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.4, random_state=1)

# Create Decision Tree classifer object
clf = DecisionTreeClassifier(criterion="entropy", max_depth=3)

# Train Decision Tree Classifer
clf = clf.fit(X, y)  # Latih model dengan data yang sudah di-encode

# Predict on training data
y_train_pred = clf.predict(X_train)

# Predict on test data
y_test_pred = clf.predict(X_test)

# # Evaluate performance on training data
# print("Training Accuracy:", metrics.accuracy_score(y_train, y_train_pred))

# # Evaluate performance on test data
# print("Testing Accuracy:", metrics.accuracy_score(y_test, y_test_pred))

training_accuracy = metrics.accuracy_score(y_train, y_train_pred)
testing_accuracy = metrics.accuracy_score(y_test, y_test_pred)

accuracy_dict = {"Training Accuracy": training_accuracy, "Testing Accuracy": testing_accuracy}

# Ubah dictionary menjadi JSON string
accuracy_json = json.dumps([ training_accuracy ,testing_accuracy]);

# Cetak JSON string
print(accuracy_json);

# # Print the decision tree rules
# def tree_to_rules(tree, feature_names):
#     tree_ = tree.tree_
#     feature_name = [
#         feature_names[i] if i != _tree.TREE_UNDEFINED else "undefined!"
#         for i in tree_.feature
#     ]
#     # print("def tree({}):".format(", ".join(feature_names)))

#     def recurse(node):
#         if tree_.feature[node] != _tree.TREE_UNDEFINED:
#             name = feature_name[node]
#             threshold = tree_.threshold[node]
#             print ("if {} <= {}:".format(name, threshold))
#             recurse(tree_.children_left[node])
#             print ("else:  # if {} > {}".format(name, threshold))
#             recurse(tree_.children_right[node])
#         else:
#             print ("return {}".format(tree_.value[node]))

#     recurse(0)

# # Use the updated feature names
# tree_to_rules(clf, feature_names_after_encoding)

# Get unique class labels from your data, excluding the column name
class_names_unique = pima.keputusan.unique()

# Plot the decision tree
plt.figure(figsize=(12, 8))  # Atur ukuran gambar jika diperlukan
plot_tree(clf, filled=True, rounded=True, feature_names=feature_names_after_encoding, class_names=class_names_unique) # Use updated feature names here as well
plt.savefig('uploads/tree.png')  # Simpan gambar langsung ke berkas
# plt.savefig('tree.png')  # Simpan gambar langsung ke berkas
# plt.show()  # Tampilkan gambar di notebook

# Simpan model ke dalam file model.dat di folder uploads
joblib.dump(clf, 'uploads/model.pkl')  # Simpan model clf